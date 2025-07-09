<?php 
namespace Aniket\LaravelWalletSystem\Services;

use Aniket\LaravelWalletSystem\Contracts\WalletInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Aniket\LaravelWalletSystem\Models\Transaction;
use Aniket\LaravelWalletSystem\Models\TransactionHistory;
use Illuminate\Support\Facades\Schema;

class WalletManager implements WalletInterface
{
    public function updateBalance($user, float $amount, string $type, string $description = '', array $meta = []): bool
{
    $userTable = config('wallet.users_table');
    $walletColumn = config('wallet.wallet_balance_column');
    $defaultBalance = config('wallet.default_wallet_amount');
    $lock = Cache::lock("wallet:{$user->id}", config('wallet.lock_duration'));

    // Check if wallet column exists
    if (!Schema::hasColumn($userTable, $walletColumn)) {
        throw new \Exception("Column '{$walletColumn}' does not exist in the '{$userTable}' table.");
    }

    // Check if required tables exist
    if (!Schema::hasTable('transactions')) {
        throw new \Exception("Table 'transactions' does not exist. Please run migrations.");
    }

    if (!Schema::hasTable('transaction_histories')) {
        throw new \Exception("Table 'transaction_histories' does not exist. Please run migrations.");
    }

    return $lock->block(config('wallet.lock_duration'), function () use ($user, $amount, $type, $description, $meta, $walletColumn, $defaultBalance) {
        DB::transaction(function () use ($user, $amount, $type, $description, $meta, $walletColumn, $defaultBalance) {
            if (!in_array($type, ['credit', 'debit'])) {
                throw new \Exception("Invalid wallet transaction type.");
            }

            // Initialize balance if null
            if (is_null($user->{$walletColumn})) {
                $user->{$walletColumn} = $defaultBalance;
            }

            $balance = $user->{$walletColumn};

            if ($type === 'debit' && $balance < $amount) {
                throw new \Exception("Insufficient balance.");
            }

            $user->{$walletColumn} = $type === 'credit'
                ? $balance + $amount
                : $balance - $amount;

            $user->save();

            // Save to transactions
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'meta' => $meta,
            ]);

            // Save to transaction history
            TransactionHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'meta' => $meta,
            ]);
        });

        return true;
    });
}

    public function credit(float $amount, string $description = '', array $meta = []): bool
    {
        return $this->updateBalance(auth()->user(), $amount, 'credit', $description, $meta);
    }

    public function debit(float $amount, string $description = '', array $meta = []): bool
    {
        return $this->updateBalance(auth()->user(), $amount, 'debit', $description, $meta);
    }

    public function refund(float $amount, string $description = '', array $meta = []): bool
    {
        return $this->updateBalance(auth()->user(), $amount, 'credit', $description, $meta + ['refund' => true]);
    }

    public function getBalance(): float
    {
        $user = auth()->user();
        $walletColumn = config('wallet.wallet_balance_column');
        return $user->{$walletColumn} ?? 0.00;
    }
}
