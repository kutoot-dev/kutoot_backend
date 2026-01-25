<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Slider;
use Illuminate\Database\Seeder;

/**
 * SliderSeeder - Seeds homepage carousel sliders with HD images.
 *
 * DEV ONLY: Creates default homepage sliders with optimized WebP images.
 */
class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing slider images...');

        $sliders = [
            [
                'id' => 1,
                'title' => 'New Collection 2026',
                'description' => 'Discover our latest arrivals with exclusive designs and premium quality materials.',
                'slider_location' => 'home',
                'serial' => 1,
                'status' => 1,
                'picsum_id' => 1011,
            ],
            [
                'id' => 2,
                'title' => 'Summer Sale',
                'description' => 'Up to 50% off on selected items. Limited time offer - Shop now!',
                'slider_location' => 'home',
                'serial' => 2,
                'status' => 1,
                'picsum_id' => 1012,
            ],
            [
                'id' => 3,
                'title' => 'Premium Quality',
                'description' => 'Experience the finest craftsmanship with our premium product range.',
                'slider_location' => 'home',
                'serial' => 3,
                'status' => 1,
                'picsum_id' => 1013,
            ],
            [
                'id' => 4,
                'title' => 'Free Shipping',
                'description' => 'Enjoy free shipping on orders over $50. Fast and reliable delivery.',
                'slider_location' => 'home',
                'serial' => 4,
                'status' => 1,
                'picsum_id' => 1015,
            ],
            [
                'id' => 5,
                'title' => 'Trending Now',
                'description' => 'Check out what\'s trending. Be the first to get the latest styles.',
                'slider_location' => 'home',
                'serial' => 5,
                'status' => 1,
                'picsum_id' => 1016,
            ],
        ];

        foreach ($sliders as $slider) {
            // Download optimized HD slider image
            $imagePath = ImageSeederHelper::ensureImage(
                'sliders',
                'slider-' . $slider['id'],
                'slider',
                $slider['picsum_id']
            );

            Slider::updateOrCreate(
                ['id' => $slider['id']],
                [
                    'title' => $slider['title'],
                    'description' => $slider['description'],
                    'image' => $imagePath,
                    'status' => $slider['status'],
                    'serial' => $slider['serial'],
                    'slider_location' => $slider['slider_location'],
                ]
            );

            $this->command->line("  ✓ Slider #{$slider['id']}: {$slider['title']}");
        }

        $this->command->info('SliderSeeder completed. ' . count($sliders) . ' sliders seeded with HD images.');
    }
}
