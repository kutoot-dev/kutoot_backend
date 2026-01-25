<?php

namespace Database\Seeders;

use App\Models\Store\AdminShopCommissionDiscount;
use App\Models\Store\Shop;
use Illuminate\Database\Seeder;

/**
 * MasterSettingsSeeder - Seeds default commission and discount settings.
 *
 * Creates global defaults and per-shop commission/discount configurations.
 */
class MasterSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'commission_percent' => 6,
            'discount_percent' => 10,
            'minimum_bill_amount' => 1000,
            'last_updated_on' => now()->toDateString(),
        ];

        // Global default (fallback when a shop-specific row doesn't exist)
        AdminShopCommissionDiscount::query()->updateOrCreate(
            ['shop_id' => null],
            $defaults
        );

        $shopCount = 0;

        // Per-shop defaults (so each shop can override independently)
        Shop::query()->select('id')->chunkById(200, function ($shops) use ($defaults, &$shopCount) {
            foreach ($shops as $shop) {
                AdminShopCommissionDiscount::query()->updateOrCreate(
                    ['shop_id' => (int) $shop->id],
                    $defaults
                );
                $shopCount++;
            }
        });

        $this->command->info("MasterSettingsSeeder completed. Global + {$shopCount} shop-specific settings created.");
    }
}
