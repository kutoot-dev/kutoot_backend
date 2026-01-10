<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use App\Models\Product;
use App\Jobs\SyncProductToZohoJob;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

          if (env('APP_ENV') === 'production') {
                    URL::forceScheme('https');
            }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Product::created(function ($product) {
        \Log::info('Product created event fired', [
            'product_id' => $product->id
        ]);

        SyncProductToZohoJob::dispatch($product);
    });

    }
}
