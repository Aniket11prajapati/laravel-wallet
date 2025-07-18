<?php

namespace Aniket\LaravelWalletSystem\Traits;

use Aniket\LaravelWalletSystem\Services\WalletManager;

trait HasWallet
{
    public function walletManager(): WalletManager
    {
        return app(WalletManager::class);
    }

    public function credit(float $amount, string $description = '', array $meta = [])
    {
        return $this->walletManager()->updateBalance($this, $amount, 'credit', $description, $meta);
    }

    public function debit(float $amount, string $description = '', array $meta = [])
    {
        return $this->walletManager()->updateBalance($this, $amount, 'debit', $description, $meta);
    }

    public function refund(float $amount, string $description = '', array $meta = [])
    {
        return $this->walletManager()->updateBalance($this, $amount, 'refund', $description, $meta);
    }
    public function updateTransactionStatus(int $transactionId, string $status): bool
    {
        return $this->walletManager()->updateTransactionStatus($transactionId, $status);
    }

    public function getWalletTransactions(bool $paginate = true, int $perPage = 15, ?string $type = null)
    {
        return $this->walletManager()->getTransactions($this, $paginate, $perPage, $type);
    }
    public function getWalletTransactionsHistory(bool $paginate = true, int $perPage = 15, ?string $type = null)
    {
        return $this->walletManager()->getTransactionsHistory($this, $paginate, $perPage, $type);
    }
public function getAllWalletTransactionsAdmin(
    bool $paginate = true,
    int $perPage = 15,
    ?string $type = null,
    ?string $status = null,
    ?int $userId = null
) {
    return $this->walletManager()->getAllTransactionsForAdmin($paginate, $perPage, $type, $status, $userId);
}

public function getAllWalletTransactionHistoriesAdmin(
    bool $paginate = true,
    int $perPage = 15,
    ?string $type = null,
    ?string $status = null,
    ?int $userId = null
) {
    return $this->walletManager()->getAllTransactionHistoriesForAdmin($paginate, $perPage, $type, $status, $userId);
}


}
