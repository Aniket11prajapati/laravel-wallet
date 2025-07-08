<?php

namespace Aniket\LaravelWalletSystem;

use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Load default config from package
        $this->mergeConfigFrom(__DIR__ . '/../config/wallet.php', 'wallet');

        // Bind WalletManager as a singleton
        $this->app->singleton(\Aniket\LaravelWalletSystem\Services\WalletManager::class);
    }

    public function boot(): void
    {
        // Auto-load migrations from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Optionally allow user to publish the config manually (if they want to override)
        $this->publishes([
            __DIR__ . '/../config/wallet.php' => config_path('wallet.php'),
        ], 'wallet-config');

        // ✅ Automatically copy config file to Laravel app if not already published
        $this->autoCopyConfigIfMissing();
    }

    protected function autoCopyConfigIfMissing(): void
    {
        $target = config_path('wallet.php');
        $source = __DIR__ . '/../config/wallet.php';

        if (!file_exists($target)) {
            @copy($source, $target);
        }
    }
}
