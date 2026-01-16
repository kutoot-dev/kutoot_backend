<?php

namespace Database\Seeders\Store;

use App\Models\Store\StoreCategory;
use Illuminate\Database\Seeder;

class StoreCategoriesSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'Restaurant',
            'Cafe',
            'Bakery',
            'Grocery',
            'Pharmacy',
            'Salon',
            'Spa',
            'Gym',
            'Electronics',
            'Fashion',
        ];

        foreach ($names as $name) {
            StoreCategory::query()->updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}


