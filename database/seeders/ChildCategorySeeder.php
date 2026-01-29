<?php

namespace Database\Seeders;

use App\Models\ChildCategory;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ChildCategorySeeder - Seeds child categories.
 *
 * DEV ONLY: Creates sample child categories for sub-categories.
 */
class ChildCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding child categories...');

        $subCategories = SubCategory::all();

        if ($subCategories->isEmpty()) {
            $this->command->warn('No sub-categories found. Please run SubCategorySeeder first.');
            return;
        }

        $childCategoriesData = [
            'Smartphones' => ['Android Phones', 'iPhones', 'Budget Phones', 'Flagship Phones'],
            'Laptops' => ['Gaming Laptops', 'Business Laptops', 'Ultrabooks', 'Chromebooks'],
            'Men Clothing' => ['T-Shirts', 'Shirts', 'Jeans', 'Jackets'],
            'Women Clothing' => ['Dresses', 'Tops', 'Pants', 'Skirts'],
            'Furniture' => ['Living Room', 'Bedroom', 'Office', 'Outdoor'],
            'Skincare' => ['Moisturizers', 'Serums', 'Cleansers', 'Sunscreen'],
            'Fitness' => ['Gym Equipment', 'Yoga', 'Cardio', 'Weight Training'],
        ];

        $childCategoryCount = 0;

        foreach ($subCategories as $subCategory) {
            $childCategories = $childCategoriesData[$subCategory->name] ?? [];

            if (empty($childCategories)) {
                continue;
            }

            foreach ($childCategories as $childCatName) {
                $slug = Str::slug($childCatName);

                ChildCategory::updateOrCreate(
                    ['slug' => $slug, 'sub_category_id' => $subCategory->id],
                    [
                        'category_id' => $subCategory->category_id,
                        'sub_category_id' => $subCategory->id,
                        'name' => $childCatName,
                        'slug' => $slug,
                        'status' => 1,
                    ]
                );

                $childCategoryCount++;
            }

            $this->command->line("  ✓ Sub-Category: {$subCategory->name} - " . count($childCategories) . " child categories");
        }

        $this->command->info("ChildCategorySeeder completed. {$childCategoryCount} child categories seeded.");
    }
}
