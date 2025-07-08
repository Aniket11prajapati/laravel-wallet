<?php 
namespace Aniket\LaravelWalletSystem\Services;

use Aniket\LaravelWalletSystem\Contracts\WalletInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Aniket\LaravelWalletSystem\Models\Transaction;
use Aniket\LaravelWalletSystem\Models\TransactionHistory;

class WalletManager implements WalletInterface
{
    public function updateBalance($user, float $amount, string $type, string $description = '', array $meta = []): bool
    {
        $userTable = config('wallet.users_table');
        $walletColumn = config('wallet.wallet_balance_column');
        $lock = Cache::lock("wallet:{$user->id}", config('wallet.lock_duration'));

        return $lock->block(config('wallet.lock_duration'), function () use ($user, $amount, $type, $description, $meta, $walletColumn) {
            DB::transaction(function () use ($user, $amount, $type, $description, $meta, $walletColumn) {
                if (!in_array($type, ['credit', 'debit'])) {
                    throw new \Exception("Invalid wallet transaction type.");
                }

                $balance = $user->{$walletColumn};
                if ($type === 'debit' && $balance < $amount) {
                    throw new \Exception("Insufficient balance.");
                }

                $user->{$walletColumn} = $type === 'credit'
                    ? $balance + $amount
                    : $balance - $amount;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $type,
                    'description' => $description,
                    'meta' => $meta,
                ]);

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
