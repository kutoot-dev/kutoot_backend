<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\StoreBanner;
use Illuminate\Database\Seeder;

/**
 * StoreBannerSeeder - Seeds store banners with responsive HD images.
 *
 * DEV ONLY: Creates responsive banners (desktop, tablet, mobile) with optimized WebP images.
 */
class StoreBannerSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing responsive store banners...');

        $banners = [
            [
                'id' => 1,
                'title' => 'Shop the Latest Trends',
                'description' => 'Discover our newest collection with exclusive styles and premium quality.',
                'location' => 'hero',
                'button_text' => 'Shop Now',
                'link' => '/shop',
                'serial' => 1,
                'status' => 1,
                'picsum_id' => 1025,
            ],
            [
                'id' => 2,
                'title' => 'Flash Sale - 50% Off',
                'description' => 'Limited time offer on selected items. Don\'t miss out!',
                'location' => 'promo',
                'button_text' => 'View Deals',
                'link' => '/sale',
                'serial' => 2,
                'status' => 1,
                'picsum_id' => 1029,
            ],
            [
                'id' => 3,
                'title' => 'New Arrivals',
                'description' => 'Be the first to explore our fresh collection.',
                'location' => 'category',
                'button_text' => 'Explore',
                'link' => '/new-arrivals',
                'serial' => 3,
                'status' => 1,
                'picsum_id' => 1031,
            ],
            [
                'id' => 4,
                'title' => 'Best Sellers',
                'description' => 'Top-rated products loved by our customers.',
                'location' => 'featured',
                'button_text' => 'Shop Best Sellers',
                'link' => '/best-sellers',
                'serial' => 4,
                'status' => 1,
                'picsum_id' => 1033,
            ],
            [
                'id' => 5,
                'title' => 'Free Shipping Worldwide',
                'description' => 'Enjoy free delivery on all orders over $50.',
                'location' => 'footer',
                'button_text' => 'Learn More',
                'link' => '/shipping',
                'serial' => 5,
                'status' => 1,
                'picsum_id' => 1035,
            ],
        ];

        foreach ($banners as $banner) {
            // Download responsive images (desktop, tablet, mobile)
            $responsiveImages = ImageSeederHelper::ensureResponsiveImages(
                'store-banners',
                'store-banner-' . $banner['id'],
                'banner',
                $banner['picsum_id']
            );

            StoreBanner::updateOrCreate(
                ['id' => $banner['id']],
                [
                    'title' => $banner['title'],
                    'description' => $banner['description'],
                    'image' => $responsiveImages['desktop'] ?? null,
                    'image_tablet' => $responsiveImages['tablet'] ?? null,
                    'image_mobile' => $responsiveImages['mobile'] ?? null,
                    'link' => $banner['link'],
                    'button_text' => $banner['button_text'],
                    'location' => $banner['location'],
                    'serial' => $banner['serial'],
                    'status' => $banner['status'],
                ]
            );

            $this->command->line("  ✓ Store Banner #{$banner['id']}: {$banner['title']} (3 responsive sizes)");
        }

        $this->command->info('StoreBannerSeeder completed. ' . count($banners) . ' banners seeded with responsive HD images.');
    }
}
