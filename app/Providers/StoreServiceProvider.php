<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * Shop and AdminShopCommissionDiscount tables have been deprecated.
     * All store data is now stored in seller_applications table.
     */
    public function boot(): void
    {
        // No longer needed - bidirectional sync removed
        // All store data now lives in SellerApplication only
    }
}
