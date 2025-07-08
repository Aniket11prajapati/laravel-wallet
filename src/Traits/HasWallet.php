<?php

namespace Aniket\Wallet\Traits;

use Aniket\Wallet\Services\WalletManager;

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
}
