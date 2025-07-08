<?php

namespace Aniket\Wallet;

use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/wallet.php' => config_path('wallet.php'),
        ], 'wallet-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wallet.php', 'wallet');

        $this->app->singleton(\Aniket\Wallet\Services\WalletManager::class);
    }
}
