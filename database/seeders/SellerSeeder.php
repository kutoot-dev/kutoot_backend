<?php

namespace Database\Seeders;

use App\Models\Store\Seller;
use App\Models\Store\SellerNotificationSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * SellerSeeder - Creates demo seller accounts for the store module.
 *
 * DEV ONLY - Do not use these credentials in production.
 *
 * Default Login Credentials:
 * - Username: seller1
 * - Password: 123456
 */
class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            [
                'username' => 'seller1',
                'seller_code' => 'SELLER001',
                'password' => Hash::make('123456'),
                'owner_name' => 'Sai Prasad',
                'email' => 'seller@kutoot.com',
                'phone' => '9812345678',
                'status' => 1,
            ],
            [
                'username' => 'seller2',
                'seller_code' => 'SELLER002',
                'password' => Hash::make('123456'),
                'owner_name' => 'Rahul Kumar',
                'email' => 'seller2@kutoot.com',
                'phone' => '9812345679',
                'status' => 1,
            ],
            [
                'username' => 'seller3',
                'seller_code' => 'SELLER003',
                'password' => Hash::make('123456'),
                'owner_name' => 'Priya Sharma',
                'email' => 'seller3@kutoot.com',
                'phone' => '9812345680',
                'status' => 1,
            ],
        ];

        foreach ($sellers as $sellerData) {
            $seller = Seller::query()->firstOrCreate(
                ['username' => $sellerData['username']],
                $sellerData
            );

            // Create notification settings for each seller
            SellerNotificationSetting::query()->firstOrCreate(
                ['seller_id' => $seller->id],
                [
                    'enabled' => true,
                    'email' => true,
                    'sms' => false,
                    'whatsapp' => true,
                ]
            );
        }

        $this->command->info('SellerSeeder completed. 3 seller accounts created.');
        $this->command->info('  - seller1 / 123456');
        $this->command->info('  - seller2 / 123456');
        $this->command->info('  - seller3 / 123456');
    }
}
