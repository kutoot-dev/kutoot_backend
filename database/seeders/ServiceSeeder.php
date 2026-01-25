<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * ServiceSeeder - Seeds service/feature icons with HD images.
 *
 * DEV ONLY: Creates sample services with optimized WebP icons for homepage features.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing service icons...');

        $services = [
            [
                'title' => 'Free Shipping',
                'description' => 'Free shipping on all orders over $50. Fast and reliable delivery to your doorstep.',
                'picsum_id' => 180,
            ],
            [
                'title' => 'Secure Payment',
                'description' => '100% secure payment with SSL encryption. We accept all major credit cards and PayPal.',
                'picsum_id' => 181,
            ],
            [
                'title' => '24/7 Support',
                'description' => 'Our customer support team is available 24/7 to help you with any questions or concerns.',
                'picsum_id' => 182,
            ],
            [
                'title' => 'Easy Returns',
                'description' => '30-day hassle-free returns. Not satisfied? Return it for a full refund, no questions asked.',
                'picsum_id' => 183,
            ],
            [
                'title' => 'Quality Guarantee',
                'description' => 'All products are quality checked before shipping. We guarantee 100% authentic products.',
                'picsum_id' => 184,
            ],
            [
                'title' => 'Member Rewards',
                'description' => 'Join our loyalty program and earn points on every purchase. Redeem for exclusive discounts.',
                'picsum_id' => 185,
            ],
        ];

        foreach ($services as $serviceData) {
            // Download service icon
            $iconPath = ImageSeederHelper::ensureImage(
                'services',
                'service-' . strtolower(str_replace(' ', '-', $serviceData['title'])),
                'service',
                $serviceData['picsum_id']
            );

            Service::updateOrCreate(
                ['title' => $serviceData['title']],
                [
                    'title' => $serviceData['title'],
                    'icon' => $iconPath,
                    'description' => $serviceData['description'],
                    'status' => 1,
                ]
            );

            $this->command->line("  ✓ Service: {$serviceData['title']}");
        }

        $this->command->info('ServiceSeeder completed. ' . count($services) . ' services seeded with HD icons.');
    }
}
