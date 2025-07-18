<?php 
namespace Aniket\LaravelWalletSystem\Services;

use Aniket\LaravelWalletSystem\Contracts\WalletInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Aniket\LaravelWalletSystem\Models\Transaction;
use Aniket\LaravelWalletSystem\Models\TransactionHistory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WalletManager implements WalletInterface
{
    public function updateBalance($user, float $amount, string $type, string $description = '', array $meta = []): bool
{
    $userTable = config('wallet.users_table');
    $walletColumn = config('wallet.wallet_balance_column');
    $defaultBalance = config('wallet.default_wallet_amount');
    $lock = Cache::lock("wallet:{$user->id}", config('wallet.lock_duration'));

    if (!Schema::hasColumn($userTable, $walletColumn)) {
        throw new \Exception("Column '{$walletColumn}' does not exist in the '{$userTable}' table.");
    }

    if (!Schema::hasTable('transactions')) {
        throw new \Exception("Table 'transactions' does not exist.");
    }

    if (!Schema::hasTable('transaction_histories')) {
        throw new \Exception("Table 'transaction_histories' does not exist.");
    }

    return $lock->block(config('wallet.lock_duration'), function () use ($user, $amount, $type, $description, $meta, $walletColumn, $defaultBalance) {
        DB::transaction(function () use ($user, $amount, $type, $description, $meta, $walletColumn, $defaultBalance) {
            if (!in_array($type, ['credit', 'debit', 'refund'])) {
                throw new \Exception("Invalid wallet transaction type.");
            }

            if (is_null($user->{$walletColumn})) {
                $user->{$walletColumn} = $defaultBalance;
            }

            $balance = $user->{$walletColumn};

            if ($type === 'debit' && $balance < $amount) {
                throw new \Exception("Insufficient balance.");
            }

            if ($type === 'credit' || $type === 'refund') {
                $user->{$walletColumn} += $amount;
            } else {
                $user->{$walletColumn} -= $amount;
            }

            $user->save();
            if (in_array($type, ['credit', 'debit'])) {
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $type,
                    'status' => 'pending',
                    'description' => $description,
                    'meta' => $meta,
                ]);
            }
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

    public function updateTransactionStatus(int $transactionId, string $status): bool
{
    $allowed = ['pending', 'processing', 'failed', 'success'];
    if (!in_array($status, $allowed)) {
        throw new \Exception("Invalid transaction status.");        
    }

    $transaction = Transaction::findOrFail($transactionId);
    $transaction->status = $status;
    $transaction->save();

    return true;
}

 public function getTransactions($user, bool $paginate = true, int $perPage = 15, ?string $type = null): Collection|LengthAwarePaginator
    {
        $query = Transaction::where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        $query->latest();

        return $paginate ? $query->paginate($perPage) : $query->get();
    }


    public function getTransactionsHistory($user, bool $paginate = true, int $perPage = 15, ?string $type = null): Collection|LengthAwarePaginator
    {
        $query = TransactionHistory::where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        $query->latest();

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    
public function getAllTransactionsForAdmin(
    bool $paginate = true,
    int $perPage = 15,
    ?string $type = null,
    ?string $status = null,
    ?int $userId = null
): Collection|LengthAwarePaginator {
    $query = Transaction::query();

    if ($type) {
        $query->where('type', $type);
    }

    if ($status) {
        $query->where('status', $status);
    }

    if ($userId) {
        $query->where('user_id', $userId); 
    }

    $query->latest();

    return $paginate ? $query->paginate($perPage) : $query->get();
}

public function getAllTransactionHistoriesForAdmin(
    bool $paginate = true,
    int $perPage = 15,
    ?string $type = null,
    ?string $status = null,
    ?int $userId = null
): Collection|LengthAwarePaginator {
    $query = TransactionHistory::query();

    if ($type) {
        $query->where('type', $type);
    }

    if ($status) {
        $query->where('status', $status);
    }

    if ($userId) {
        $query->where('user_id', $userId); 
    }

    $query->latest();

    return $paginate ? $query->paginate($perPage) : $query->get();
}


}
