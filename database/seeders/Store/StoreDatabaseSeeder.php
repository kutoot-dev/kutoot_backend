<?php

namespace Database\Seeders\Store;

use Illuminate\Database\Seeder;

class StoreDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            StoreCategoriesSeeder::class,
            StoreDemoSellerSeeder::class,
            StoreMasterAdminSettingsSeeder::class,
            StoreDemoVisitorsSeeder::class,
            StoreDemoBulkTransactionsSeeder::class,
            StoreCoinCampaignSeeder::class,
            StorePurchasedCoinsSeeder::class,
            StoreUserCoinsSeeder::class,
        ]);
    }
}


