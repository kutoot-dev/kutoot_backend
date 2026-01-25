<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BlogCategorySeeder - Seeds blog categories.
 *
 * DEV ONLY: Creates sample blog categories for the blog module.
 */
class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding blog categories...');

        $categories = [
            ['name' => 'Technology', 'status' => 1],
            ['name' => 'Lifestyle', 'status' => 1],
            ['name' => 'Fashion Tips', 'status' => 1],
            ['name' => 'Health & Wellness', 'status' => 1],
            ['name' => 'Product Reviews', 'status' => 1],
            ['name' => 'How-To Guides', 'status' => 1],
            ['name' => 'News & Updates', 'status' => 1],
            ['name' => 'Shopping Tips', 'status' => 1],
        ];

        foreach ($categories as $category) {
            BlogCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'status' => $category['status'],
                ]
            );

            $this->command->line("  ✓ Blog Category: {$category['name']}");
        }

        $this->command->info('BlogCategorySeeder completed. ' . count($categories) . ' categories seeded.');
    }
}
