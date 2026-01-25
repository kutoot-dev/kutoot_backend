<?php

namespace Database\Seeders;

use App\Models\Store\Seller;
use App\Models\Store\Shop;
use Illuminate\Database\Seeder;

/**
 * ShopSeeder - Creates demo shops linked to sellers.
 *
 * DEV ONLY - Creates shops for testing store functionality.
 */
class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $shops = [
            [
                'seller_code' => 'SELLER001',
                'shop_code' => 'SHOP001',
                'shop_name' => 'Gourmet Delight',
                'category' => 'Restaurant',
                'gst_number' => '07ABCDE1234F1Z5',
                'address' => 'Connaught Place, New Delhi',
                'location_lat' => 28.6315,
                'location_lng' => 77.2197,
            ],
            [
                'seller_code' => 'SELLER002',
                'shop_code' => 'SHOP002',
                'shop_name' => 'Coffee Corner',
                'category' => 'Cafe',
                'gst_number' => '07FGHIJ5678K2L6',
                'address' => 'Hauz Khas, New Delhi',
                'location_lat' => 28.5494,
                'location_lng' => 77.2001,
            ],
            [
                'seller_code' => 'SELLER003',
                'shop_code' => 'SHOP003',
                'shop_name' => 'Fresh Mart',
                'category' => 'Grocery',
                'gst_number' => '07MNOPQ9012R3S7',
                'address' => 'Saket, New Delhi',
                'location_lat' => 28.5245,
                'location_lng' => 77.2066,
            ],
        ];

        foreach ($shops as $shopData) {
            $seller = Seller::query()->where('seller_code', $shopData['seller_code'])->first();

            if (!$seller) {
                $this->command->warn("Seller with code {$shopData['seller_code']} not found. Skipping shop {$shopData['shop_name']}.");
                continue;
            }

            Shop::query()->firstOrCreate(
                ['seller_id' => $seller->id],
                [
                    'shop_code' => $shopData['shop_code'],
                    'shop_name' => $shopData['shop_name'],
                    'category' => $shopData['category'],
                    'owner_name' => $seller->owner_name,
                    'email' => $seller->email,
                    'phone' => $seller->phone,
                    'gst_number' => $shopData['gst_number'],
                    'address' => $shopData['address'],
                    'location_lat' => $shopData['location_lat'],
                    'location_lng' => $shopData['location_lng'],
                ]
            );
        }

        $this->command->info('ShopSeeder completed. 3 shops created.');
    }
}
