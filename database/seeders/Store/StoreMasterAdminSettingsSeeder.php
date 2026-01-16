<?php

namespace Database\Seeders\Store;

use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Shop;
use Illuminate\Database\Seeder;

class StoreMasterAdminSettingsSeeder extends Seeder
{
    public function run()
    {
        // Fresh project defaults:
        // - min bill amount: 1000 (below this, discount/coins should be 0)
        $defaults = [
            'commission_percent' => 6,
            'discount_percent' => 10,
            'minimum_bill_amount' => 1000,
            'last_updated_on' => now()->toDateString(),
        ];

        // Global default (fallback when a shop-specific row doesn't exist yet)
        AdminShopCommissionDiscount::query()->updateOrCreate(
            ['shop_id' => null],
            $defaults
        );

        // Per-shop defaults (so each shop can override independently)
        Shop::query()->select('id')->chunkById(200, function ($shops) use ($defaults) {
            foreach ($shops as $shop) {
                AdminShopCommissionDiscount::query()->updateOrCreate(
                    ['shop_id' => (int) $shop->id],
                    $defaults
                );
            }
        });
    }
}


