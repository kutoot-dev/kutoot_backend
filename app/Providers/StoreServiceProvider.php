<?php

namespace App\Providers;

use App\Models\Store\SellerApplication;
use App\Models\Store\Shop;
use App\Observers\Store\StoreDetailsSyncObserver;
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
     * Registers observers for bidirectional sync between SellerApplication and Shop.
     */
    public function boot(): void
    {
        $observer = new StoreDetailsSyncObserver();

        // Register observer for SellerApplication updates
        SellerApplication::updated(function (SellerApplication $application) use ($observer) {
            $observer->applicationUpdated($application);
        });

        // Register observer for Shop updates
        Shop::updated(function (Shop $shop) use ($observer) {
            $observer->shopUpdated($shop);
        });
    }
}
