<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * VendorSeeder - Seeds multi-vendor marketplace vendors with HD images.
 *
 * DEV ONLY: Creates sample vendors with optimized WebP logos and banners.
 */
class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing vendor images...');

        $vendors = [
            [
                'shop_name' => 'TechZone Electronics',
                'owner_name' => 'Raj Patel',
                'description' => 'Your one-stop shop for the latest electronics and gadgets. We offer premium quality products at competitive prices.',
                'picsum_id' => 1,
            ],
            [
                'shop_name' => 'Fashion Hub',
                'owner_name' => 'Priya Sharma',
                'description' => 'Trendy fashion for men, women and kids. Stay stylish with our curated collection of clothing and accessories.',
                'picsum_id' => 669,
            ],
            [
                'shop_name' => 'Home Essentials',
                'owner_name' => 'Amit Kumar',
                'description' => 'Transform your living space with our premium home decor, furniture and kitchen essentials.',
                'picsum_id' => 49,
            ],
            [
                'shop_name' => 'Sports Arena',
                'owner_name' => 'Vikram Singh',
                'description' => 'Quality sports equipment and fitness gear for athletes and enthusiasts. Get fit with the best.',
                'picsum_id' => 222,
            ],
            [
                'shop_name' => 'Beauty Palace',
                'owner_name' => 'Neha Gupta',
                'description' => 'Premium skincare, makeup and wellness products. Look and feel your best every day.',
                'picsum_id' => 64,
            ],
        ];

        // Get existing users or create new ones
        $users = User::take(count($vendors))->get();

        if ($users->count() < count($vendors)) {
            $this->command->warn('Not enough users found. Creating additional users...');
            $additionalUsers = User::factory(count($vendors) - $users->count())->create();
            $users = $users->merge($additionalUsers);
        }

        foreach ($vendors as $index => $vendorData) {
            $user = $users[$index];
            $slug = Str::slug($vendorData['shop_name']);

            // Download vendor logo
            $logoPath = ImageSeederHelper::ensureImage(
                'vendors',
                'logo-' . $slug,
                'logo',
                $vendorData['picsum_id']
            );

            // Download vendor banner
            $bannerPath = ImageSeederHelper::ensureImage(
                'vendors',
                'banner-' . $slug,
                'banner',
                $vendorData['picsum_id']
            );

            Vendor::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $user->id,
                    'shop_name' => $vendorData['shop_name'],
                    'slug' => $slug,
                    'owner_name' => $vendorData['owner_name'],
                    'email' => strtolower(str_replace(' ', '', $vendorData['owner_name'])) . '@vendor.com',
                    'phone' => '98' . rand(10000000, 99999999),
                    'address' => 'Block ' . chr(65 + $index) . ', Commercial Complex, New Delhi',
                    'description' => $vendorData['description'],
                    'greeting_msg' => 'Welcome to ' . $vendorData['shop_name'] . '!',
                    'open_at' => '09:00',
                    'closed_at' => '21:00',
                    'address_latitude' => 28.6 + ($index * 0.01),
                    'address_longitude' => 77.2 + ($index * 0.01),
                    'status' => 1,
                    'is_featured' => $index < 3 ? 1 : 0,
                    'is_top' => $index < 2 ? 1 : 0,
                    'banner_image' => $bannerPath,
                    'logo' => $logoPath,
                ]
            );

            $this->command->line("  ✓ Vendor: {$vendorData['shop_name']}");
        }

        $this->command->info('VendorSeeder completed. ' . count($vendors) . ' vendors seeded with HD images.');
    }
}
