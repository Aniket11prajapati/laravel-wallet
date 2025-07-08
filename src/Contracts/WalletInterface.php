<?php

namespace Aniket\LaravelWalletSystem\Contracts;

interface WalletInterface
{
    public function credit(float $amount, string $description = '', array $meta = []): bool;

    public function debit(float $amount, string $description = '', array $meta = []): bool;

    public function refund(float $amount, string $description = '', array $meta = []): bool;

    public function getBalance(): float;
}
