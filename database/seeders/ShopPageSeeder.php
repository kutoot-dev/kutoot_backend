<?php

namespace Database\Seeders;

use App\Models\ShopPage;
use Illuminate\Database\Seeder;

/**
 * ShopPageSeeder - Dev only
 * Seeds default shop page settings with filter price range
 */
class ShopPageSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating shop page settings...');

        ShopPage::updateOrCreate(
            ['id' => 1],
            [
                'header_one' => 'Shop Our Collection',
                'header_two' => 'Find Your Perfect Product',
                'title_one' => 'Quality Products',
                'title_two' => 'Best Prices',
                'banner' => null,
                'link' => null,
                'button_text' => 'Shop Now',
                'filter_price_range' => 1000,
            ]
        );

        $this->command->info('ShopPageSeeder completed. Default shop page settings created.');
    }
}
