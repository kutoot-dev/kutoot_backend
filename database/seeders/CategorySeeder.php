<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CategorySeeder - Seeds product categories with HD images and icons.
 *
 * DEV ONLY: Creates product categories with optimized WebP images.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing category images...');

        $categories = [
            [
                'name' => 'Electronics',
                'is_featured' => 1,
                'is_top' => 1,
                'picsum_id' => 0,
                'subcategories' => ['Smartphones', 'Laptops', 'Tablets', 'Accessories'],
            ],
            [
                'name' => 'Fashion',
                'is_featured' => 1,
                'is_popular' => 1,
                'picsum_id' => 669,
                'subcategories' => ['Men\'s Wear', 'Women\'s Wear', 'Kids Wear', 'Footwear'],
            ],
            [
                'name' => 'Home & Living',
                'is_featured' => 1,
                'picsum_id' => 49,
                'subcategories' => ['Furniture', 'Decor', 'Kitchen', 'Bedding'],
            ],
            [
                'name' => 'Sports & Fitness',
                'is_top' => 1,
                'picsum_id' => 222,
                'subcategories' => ['Gym Equipment', 'Sportswear', 'Outdoor', 'Yoga'],
            ],
            [
                'name' => 'Beauty & Health',
                'is_popular' => 1,
                'picsum_id' => 64,
                'subcategories' => ['Skincare', 'Makeup', 'Haircare', 'Wellness'],
            ],
            [
                'name' => 'Books & Stationery',
                'picsum_id' => 24,
                'subcategories' => ['Fiction', 'Non-Fiction', 'Office Supplies', 'Art Supplies'],
            ],
            [
                'name' => 'Toys & Games',
                'is_trending' => 1,
                'picsum_id' => 174,
                'subcategories' => ['Action Figures', 'Board Games', 'Educational', 'Outdoor Toys'],
            ],
            [
                'name' => 'Jewelry & Watches',
                'is_featured' => 1,
                'picsum_id' => 452,
                'subcategories' => ['Rings', 'Necklaces', 'Watches', 'Bracelets'],
            ],
            [
                'name' => 'Automotive',
                'picsum_id' => 133,
                'subcategories' => ['Car Accessories', 'Bike Accessories', 'Tools', 'Care Products'],
            ],
            [
                'name' => 'Grocery',
                'is_top' => 1,
                'picsum_id' => 429,
                'subcategories' => ['Snacks', 'Beverages', 'Fresh Produce', 'Pantry'],
            ],
        ];

        foreach ($categories as $categoryData) {
            // Download category logo
            $logoPath = ImageSeederHelper::ensureImage(
                'categories',
                'logo-' . Str::slug($categoryData['name']),
                'category',
                $categoryData['picsum_id']
            );

            // Download category icon (smaller version)
            $iconPath = ImageSeederHelper::ensureImage(
                'categories',
                'icon-' . Str::slug($categoryData['name']),
                'icon',
                $categoryData['picsum_id']
            );

            // Download category banner image
            $imagePath = ImageSeederHelper::ensureImage(
                'categories',
                'image-' . Str::slug($categoryData['name']),
                'category',
                $categoryData['picsum_id']
            );

            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'slug' => Str::slug($categoryData['name']),
                    'logo' => $logoPath,
                    'icon' => $iconPath,
                    'image' => $imagePath,
                    'status' => 1,
                    'is_featured' => $categoryData['is_featured'] ?? 0,
                    'is_top' => $categoryData['is_top'] ?? 0,
                    'is_popular' => $categoryData['is_popular'] ?? 0,
                    'is_trending' => $categoryData['is_trending'] ?? 0,
                ]
            );

            $this->command->line("  ✓ Category: {$categoryData['name']}");

            // Create subcategories
            if (isset($categoryData['subcategories'])) {
                foreach ($categoryData['subcategories'] as $subName) {
                    SubCategory::updateOrCreate(
                        ['slug' => Str::slug($subName), 'category_id' => $category->id],
                        [
                            'name' => $subName,
                            'slug' => Str::slug($subName),
                            'category_id' => $category->id,
                            'status' => 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('CategorySeeder completed. ' . count($categories) . ' categories seeded with HD images.');
    }
}
