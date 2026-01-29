<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\MasterPrize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * MasterPrizeSeeder - Seeds master prize catalog with HD ecommerce images.
 *
 * DEV ONLY: Creates sample prizes with optimized WebP images.
 */
class MasterPrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Downloading and optimizing prize images...');

        $prizes = [
            [
                'title' => 'iPhone 15 Pro Max',
                'description' => 'Latest Apple iPhone 15 Pro Max 256GB - Titanium Blue. Features A17 Pro chip, 48MP camera system, and titanium design.',
                'picsum_id' => 1,
            ],
            [
                'title' => 'MacBook Air M3',
                'description' => 'Apple MacBook Air 15" M3 chip - 8GB RAM, 256GB SSD. Ultra-thin, powerful, with all-day battery life.',
                'picsum_id' => 2,
            ],
            [
                'title' => 'Samsung 65" OLED TV',
                'description' => 'Samsung 65" Class OLED S95C Series 4K Smart TV. Stunning picture quality with infinite contrast.',
                'picsum_id' => 3,
            ],
            [
                'title' => 'PlayStation 5 Pro',
                'description' => 'PS5 Pro Console with extra controller and 3 premium games. Experience next-gen gaming.',
                'picsum_id' => 4,
            ],
            [
                'title' => 'Apple Watch Ultra 2',
                'description' => 'Apple Watch Ultra 2 with Ocean Band. Built for extreme adventures with precision GPS.',
                'picsum_id' => 5,
            ],
            [
                'title' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Sony WH-1000XM5 Wireless Noise Cancelling Headphones - Industry-leading noise cancellation.',
                'picsum_id' => 6,
            ],
            [
                'title' => '$1000 Shopping Spree',
                'description' => 'Gift Card worth $1000 for shopping at any of our partner stores.',
                'picsum_id' => 7,
            ],
            [
                'title' => 'iPad Pro 12.9"',
                'description' => 'iPad Pro 12.9" M2 chip - 256GB WiFi. The ultimate iPad experience with Liquid Retina XDR display.',
                'picsum_id' => 8,
            ],
            [
                'title' => 'DJI Mavic 3 Pro Drone',
                'description' => 'DJI Mavic 3 Pro with Fly More Combo. Professional aerial photography made easy.',
                'picsum_id' => 9,
            ],
            [
                'title' => 'Nintendo Switch OLED Bundle',
                'description' => 'Nintendo Switch OLED Model with 5 games bundle. Perfect for gaming on-the-go.',
                'picsum_id' => 10,
            ],
            [
                'title' => 'Canon EOS R6 Mark II',
                'description' => 'Canon EOS R6 Mark II with RF 24-105mm lens. Professional mirrorless camera for stunning photos.',
                'picsum_id' => 11,
            ],
            [
                'title' => 'Luxury Watch Collection',
                'description' => 'Premium luxury watch from our curated collection. Timeless elegance on your wrist.',
                'picsum_id' => 12,
            ],
        ];

        foreach ($prizes as $prizeData) {
            $slug = Str::slug($prizeData['title']);

            // Download prize image
            $imgPath = ImageSeederHelper::ensureImage(
                'prizes',
                'prize-' . $slug,
                'product',
                $prizeData['picsum_id']
            );

            MasterPrize::updateOrCreate(
                ['title' => $prizeData['title']],
                [
                    'title' => $prizeData['title'],
                    'description' => $prizeData['description'],
                    'img' => $imgPath,
                    'status' => 1,
                ]
            );

            $this->command->line("  ✓ Prize: {$prizeData['title']}");
        }

        $this->command->info('MasterPrizeSeeder completed. ' . count($prizes) . ' prizes seeded with HD images.');
    }
}
