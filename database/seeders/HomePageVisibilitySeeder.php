<?php

namespace Database\Seeders;

use App\Models\HomePageOneVisibility;
use Illuminate\Database\Seeder;

/**
 * HomePageVisibilitySeeder - Seeds homepage section visibility settings.
 *
 * DEV ONLY: Creates default visibility settings for homepage sections.
 * Required for HomeController to function without null errors.
 */
class HomePageVisibilitySeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['id' => 1, 'section_name' => 'Slider', 'status' => 1, 'qty' => 5],
            ['id' => 2, 'section_name' => 'Services', 'status' => 1, 'qty' => 4],
            ['id' => 3, 'section_name' => 'Reserved', 'status' => 1, 'qty' => 4],
            ['id' => 4, 'section_name' => 'Popular Categories', 'status' => 1, 'qty' => 8],
            ['id' => 5, 'section_name' => 'Brands', 'status' => 1, 'qty' => 12],
            ['id' => 6, 'section_name' => 'Top Rated Products', 'status' => 1, 'qty' => 8],
            ['id' => 7, 'section_name' => 'Sellers', 'status' => 1, 'qty' => 6],
            ['id' => 8, 'section_name' => 'Featured Products', 'status' => 1, 'qty' => 8],
            ['id' => 9, 'section_name' => 'New Arrival Products', 'status' => 1, 'qty' => 8],
            ['id' => 10, 'section_name' => 'Best Products', 'status' => 1, 'qty' => 8],
        ];

        foreach ($sections as $section) {
            HomePageOneVisibility::updateOrCreate(
                ['id' => $section['id']],
                $section
            );
        }

        $this->command->info('HomePageVisibilitySeeder completed. ' . count($sections) . ' sections seeded.');
    }
}
