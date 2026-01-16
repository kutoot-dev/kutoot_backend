<?php

namespace Database\Seeders\Store;

use App\Models\Store\Seller;
use App\Models\Store\Shop;
use App\Models\Store\SellerNotificationSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDemoSellerSeeder extends Seeder
{
    public function run()
    {
        $seller = Seller::query()->firstOrCreate(
            ['username' => 'seller1'],
            [
                'seller_code' => 'SELLER001',
                'password' => Hash::make('123456'),
                'owner_name' => 'Sai Prasad',
                'email' => 'seller@kutoot.com',
                'phone' => '98XXXXXX12',
                'status' => 1,
            ]
        );

        Shop::query()->firstOrCreate(
            ['seller_id' => $seller->id],
            [
                'shop_code' => 'SHOP001',
                'shop_name' => 'Gourmet Delight',
                'category' => 'Restaurant',
                'owner_name' => $seller->owner_name,
                'email' => $seller->email,
                'phone' => '9898989898',
                'gst_number' => '07ABCDE1234F1Z5',
                'address' => 'Connaught Place, New Delhi',
                'location_lat' => 28.6315,
                'location_lng' => 77.2197,
            ]
        );

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
}


