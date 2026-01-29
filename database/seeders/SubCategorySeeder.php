<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * SubCategorySeeder - Seeds sub-categories.
 *
 * DEV ONLY: Creates sample sub-categories for each product category.
 */
class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding sub-categories...');

        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $subCategoriesData = [
            'Electronics' => ['Smartphones', 'Laptops', 'Tablets', 'Audio', 'Cameras'],
            'Fashion' => ['Men Clothing', 'Women Clothing', 'Kids Wear', 'Footwear', 'Accessories'],
            'Home & Living' => ['Furniture', 'Decor', 'Kitchen', 'Bedding', 'Lighting'],
            'Beauty' => ['Skincare', 'Makeup', 'Haircare', 'Fragrances', 'Personal Care'],
            'Sports' => ['Fitness', 'Outdoor', 'Team Sports', 'Water Sports', 'Winter Sports'],
        ];

        $subCategoryCount = 0;

        foreach ($categories as $category) {
            $subCategories = $subCategoriesData[$category->name] ?? ['General', 'Featured', 'New Arrivals'];

            foreach ($subCategories as $subCatName) {
                $slug = Str::slug($subCatName);

                SubCategory::updateOrCreate(
                    ['slug' => $slug, 'category_id' => $category->id],
                    [
                        'category_id' => $category->id,
                        'name' => $subCatName,
                        'slug' => $slug,
                        'status' => 1,
                    ]
                );

                $subCategoryCount++;
            }

            $this->command->line("  ✓ Category: {$category->name} - " . count($subCategories) . " sub-categories");
        }

        $this->command->info("SubCategorySeeder completed. {$subCategoryCount} sub-categories seeded.");
    }
}
