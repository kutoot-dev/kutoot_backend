<?php

namespace Database\Seeders;

use App\Models\Store\Seller;
use App\Models\Store\SellerApplication;
use Illuminate\Database\Seeder;

/**
 * ShopSeeder - Updates seller applications to mark them as approved stores.
 *
 * DEV ONLY - Sets up approved store data for testing.
 */
class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'seller_code' => 'SELLER001',
                'shop_code' => 'SHOP001',
                'store_name' => 'Gourmet Delight',
                'store_type' => 'Restaurant',
                'gst_number' => '07ABCDE1234F1Z5',
                'store_address' => 'Connaught Place, New Delhi',
                'lat' => 28.6315,
                'lng' => 77.2197,
            ],
            [
                'seller_code' => 'SELLER002',
                'shop_code' => 'SHOP002',
                'store_name' => 'Coffee Corner',
                'store_type' => 'Cafe',
                'gst_number' => '07FGHIJ5678K2L6',
                'store_address' => 'Hauz Khas, New Delhi',
                'lat' => 28.5494,
                'lng' => 77.2001,
            ],
            [
                'seller_code' => 'SELLER003',
                'shop_code' => 'SHOP003',
                'store_name' => 'Fresh Mart',
                'store_type' => 'Grocery',
                'gst_number' => '07MNOPQ9012R3S7',
                'store_address' => 'Saket, New Delhi',
                'lat' => 28.5245,
                'lng' => 77.2066,
            ],
        ];

        foreach ($stores as $storeData) {
            $seller = Seller::query()->where('seller_code', $storeData['seller_code'])->first();

            if (!$seller) {
                $this->command->warn("Seller with code {$storeData['seller_code']} not found. Skipping store {$storeData['store_name']}.");
                continue;
            }

            SellerApplication::query()->updateOrCreate(
                ['seller_id' => $seller->id],
                [
                    'shop_code' => $storeData['shop_code'],
                    'store_name' => $storeData['store_name'],
                    'store_type' => $storeData['store_type'],
                    'owner_name' => $seller->owner_name,
                    'owner_email' => $seller->email,
                    'owner_mobile' => $seller->phone,
                    'gst_number' => $storeData['gst_number'],
                    'store_address' => $storeData['store_address'],
                    'lat' => $storeData['lat'],
                    'lng' => $storeData['lng'],
                    'status' => 'APPROVED',
                    'verified_at' => now(),
                    'approved_at' => now(),
                    'commission_percent' => 6,
                    'discount_percent' => 10,
                    'min_bill_amount' => 1000,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('ShopSeeder completed. 3 stores created/updated in seller_applications.');
    }
}
