<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Store\StoreCategory;
use Illuminate\Database\Seeder;

/**
 * StoreCategorySeeder - Seeds store categories with HD images and icons.
 */
class StoreCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing store category images...');

        $categories = [
            ['name' => 'Restaurant', 'picsum_id' => 292],
            ['name' => 'Cafe', 'picsum_id' => 312],
            ['name' => 'Bakery', 'picsum_id' => 326],
            ['name' => 'Grocery', 'picsum_id' => 429],
            ['name' => 'Pharmacy', 'picsum_id' => 180],
            ['name' => 'Salon', 'picsum_id' => 64],
            ['name' => 'Spa', 'picsum_id' => 188],
            ['name' => 'Gym', 'picsum_id' => 173],
            ['name' => 'Electronics', 'picsum_id' => 0],
            ['name' => 'Fashion', 'picsum_id' => 669],
            ['name' => 'Books', 'picsum_id' => 24],
            ['name' => 'Home Decor', 'picsum_id' => 49],
            ['name' => 'Jewelry', 'picsum_id' => 452],
            ['name' => 'Pet Store', 'picsum_id' => 237],
            ['name' => 'Sports', 'picsum_id' => 222],
        ];

        foreach ($categories as $index => $category) {
            // Download category image
            $imagePath = ImageSeederHelper::ensureImage(
                'store-categories',
                'category-' . strtolower(str_replace(' ', '-', $category['name'])),
                'category',
                $category['picsum_id']
            );

            // Download category icon
            $iconPath = ImageSeederHelper::ensureImage(
                'store-categories',
                'icon-' . strtolower(str_replace(' ', '-', $category['name'])),
                'icon',
                $category['picsum_id']
            );

            StoreCategory::query()->updateOrCreate(
                ['name' => $category['name']],
                [
                    'image' => $imagePath,
                    'icon' => $iconPath,
                    'serial' => $index + 1,
                    'is_active' => true,
                ]
            );

            $this->command->line("  ✓ Category: {$category['name']}");
        }

        $this->command->info('StoreCategorySeeder completed. ' . count($categories) . ' categories seeded with HD images.');
    }
}
