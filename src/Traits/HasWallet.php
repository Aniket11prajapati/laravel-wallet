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
        return $this->walletManager()->refund($this, $amount, $description, $meta);
    }
     public function updateTransactionStatus(int $transactionId, string $status): bool
    {
        return $this->walletManager()->updateTransactionStatus($transactionId, $status);
    }
}
