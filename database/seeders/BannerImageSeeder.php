<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\BannerImage;
use Illuminate\Database\Seeder;

/**
 * BannerImageSeeder - Seeds banner images for homepage with HD images.
 *
 * DEV ONLY: Creates default banner images required by HomeController.
 * IDs 16 and 17 are specifically required for slider banners.
 * Images are downloaded from Lorem Picsum and optimized as WebP.
 */
class BannerImageSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing banner images...');

        $banners = [
            // First 15 general banners
            ['id' => 1, 'header' => 'Main', 'title' => 'Welcome Banner', 'title_one' => 'Welcome to', 'title_two' => 'Kutoot', 'badge' => 'New', 'banner_location' => 'home_top', 'status' => 1],
            ['id' => 2, 'header' => 'Promo', 'title' => 'Promo Banner', 'title_one' => 'Special', 'title_two' => 'Offers', 'badge' => 'Hot', 'banner_location' => 'home_middle', 'status' => 1],
            ['id' => 3, 'header' => 'Category', 'title' => 'Category Banner 1', 'title_one' => 'Shop by', 'title_two' => 'Category', 'badge' => '', 'banner_location' => 'category', 'status' => 1],
            ['id' => 4, 'header' => 'Category', 'title' => 'Category Banner 2', 'title_one' => 'Featured', 'title_two' => 'Items', 'badge' => '', 'banner_location' => 'category', 'status' => 1],
            ['id' => 5, 'header' => 'Sale', 'title' => 'Sale Banner', 'title_one' => 'Big', 'title_two' => 'Sale', 'badge' => '50% Off', 'banner_location' => 'sale', 'status' => 1],
            ['id' => 6, 'header' => 'Footer', 'title' => 'Footer Banner 1', 'title_one' => 'Subscribe', 'title_two' => 'Newsletter', 'badge' => '', 'banner_location' => 'footer', 'status' => 1],
            ['id' => 7, 'header' => 'Footer', 'title' => 'Footer Banner 2', 'title_one' => 'Follow', 'title_two' => 'Us', 'badge' => '', 'banner_location' => 'footer', 'status' => 1],
            ['id' => 8, 'header' => 'Sidebar', 'title' => 'Sidebar Banner 1', 'title_one' => 'Trending', 'title_two' => 'Now', 'badge' => 'Hot', 'banner_location' => 'sidebar', 'status' => 1],
            ['id' => 9, 'header' => 'Sidebar', 'title' => 'Sidebar Banner 2', 'title_one' => 'Best', 'title_two' => 'Sellers', 'badge' => '', 'banner_location' => 'sidebar', 'status' => 1],
            ['id' => 10, 'header' => 'Collection', 'title' => 'Collection Banner', 'title_one' => 'New', 'title_two' => 'Collection', 'badge' => '2026', 'banner_location' => 'collection', 'status' => 1],
            ['id' => 11, 'header' => 'Brand', 'title' => 'Brand Banner 1', 'title_one' => 'Top', 'title_two' => 'Brands', 'badge' => '', 'banner_location' => 'brand', 'status' => 1],
            ['id' => 12, 'header' => 'Brand', 'title' => 'Brand Banner 2', 'title_one' => 'Premium', 'title_two' => 'Quality', 'badge' => '', 'banner_location' => 'brand', 'status' => 1],
            ['id' => 13, 'header' => 'Offer', 'title' => 'Offer Banner 1', 'title_one' => 'Limited', 'title_two' => 'Time', 'badge' => 'Hurry', 'banner_location' => 'offer', 'status' => 1],
            ['id' => 14, 'header' => 'Offer', 'title' => 'Offer Banner 2', 'title_one' => 'Flash', 'title_two' => 'Sale', 'badge' => 'Today', 'banner_location' => 'offer', 'status' => 1],
            ['id' => 15, 'header' => 'Promo', 'title' => 'Promo Banner 2', 'title_one' => 'Exclusive', 'title_two' => 'Deals', 'badge' => 'VIP', 'banner_location' => 'promo', 'status' => 1],

            // IDs 16-20 are required by HomeController for slider/column banners
            ['id' => 16, 'header' => 'Slider', 'title' => 'Slider Banner One', 'title_one' => 'Discover', 'title_two' => 'Amazing Deals', 'badge' => 'Featured', 'product_slug' => null, 'banner_location' => 'slider', 'status' => 1],
            ['id' => 17, 'header' => 'Slider', 'title' => 'Slider Banner Two', 'title_one' => 'Shop', 'title_two' => 'The Collection', 'badge' => 'New', 'product_slug' => null, 'banner_location' => 'slider', 'status' => 1],
            ['id' => 18, 'header' => 'Column', 'title' => 'Three Column Banner 1', 'title_one' => 'Free', 'title_two' => 'Shipping', 'badge' => '', 'banner_location' => 'three_column', 'status' => 1],
            ['id' => 19, 'header' => 'Column', 'title' => 'Two Column Banner 1', 'title_one' => 'New', 'title_two' => 'Arrivals', 'badge' => 'Hot', 'banner_location' => 'two_column', 'status' => 1],
            ['id' => 20, 'header' => 'Column', 'title' => 'Two Column Banner 2', 'title_one' => 'Best', 'title_two' => 'Sellers', 'badge' => '', 'banner_location' => 'two_column', 'status' => 1],

            // IDs 21-30 additional banners for other homepage sections
            ['id' => 21, 'header' => 'Feature', 'title' => 'Feature Banner 1', 'title_one' => 'Quality', 'title_two' => 'Products', 'badge' => '', 'banner_location' => 'feature', 'status' => 1],
            ['id' => 22, 'header' => 'Feature', 'title' => 'Feature Banner 2', 'title_one' => 'Fast', 'title_two' => 'Delivery', 'badge' => '', 'banner_location' => 'feature', 'status' => 1],
            ['id' => 23, 'header' => 'Feature', 'title' => 'Feature Banner 3', 'title_one' => '24/7', 'title_two' => 'Support', 'badge' => '', 'banner_location' => 'feature', 'status' => 1],
            ['id' => 24, 'header' => 'Popup', 'title' => 'Popup Banner', 'title_one' => 'Subscribe', 'title_two' => 'Now', 'badge' => 'Exclusive', 'banner_location' => 'popup', 'status' => 1],
            ['id' => 25, 'header' => 'Hero', 'title' => 'Hero Banner 1', 'title_one' => 'Shop', 'title_two' => 'Now', 'badge' => 'Featured', 'banner_location' => 'hero', 'status' => 1],
            ['id' => 26, 'header' => 'Hero', 'title' => 'Hero Banner 2', 'title_one' => 'Discover', 'title_two' => 'More', 'badge' => '', 'banner_location' => 'hero', 'status' => 1],
            ['id' => 27, 'header' => 'Services', 'title' => 'Services Banner', 'title_one' => 'Our', 'title_two' => 'Services', 'badge' => '', 'banner_location' => 'services', 'status' => 1],
            ['id' => 28, 'header' => 'Contact', 'title' => 'Contact Banner', 'title_one' => 'Get in', 'title_two' => 'Touch', 'badge' => '', 'banner_location' => 'contact', 'status' => 1],
            ['id' => 29, 'header' => 'About', 'title' => 'About Banner', 'title_one' => 'About', 'title_two' => 'Us', 'badge' => '', 'banner_location' => 'about', 'status' => 1],
            ['id' => 30, 'header' => 'Blog', 'title' => 'Blog Banner', 'title_one' => 'Latest', 'title_two' => 'News', 'badge' => '', 'banner_location' => 'blog', 'status' => 1],
        ];

        foreach ($banners as $banner) {
            // Download optimized HD image for each banner
            $imageType = in_array($banner['banner_location'], ['slider']) ? 'slider' : 'banner';
            $imagePath = ImageSeederHelper::ensureImage(
                'banners',
                'banner-' . $banner['id'],
                $imageType,
                $this->getPicsumIdForBanner($banner['id'])
            );

            BannerImage::updateOrCreate(
                ['id' => $banner['id']],
                array_merge($banner, [
                    'image' => $imagePath,
                    'link' => null,
                    'button_text' => 'Shop Now',
                    'details' => 'Banner description for ' . $banner['title'],
                ])
            );

            $this->command->line("  ✓ Banner #{$banner['id']}: {$banner['title']}");
        }

        $this->command->info('BannerImageSeeder completed. ' . count($banners) . ' banners seeded with HD images.');
    }

    /**
     * Get consistent Picsum photo ID for each banner
     */
    private function getPicsumIdForBanner(int $bannerId): int
    {
        // Map banner IDs to specific Picsum photo IDs for consistent, ecommerce-appropriate images
        $photoMap = [
            1 => 1011, 2 => 1029, 3 => 1033, 4 => 1035, 5 => 1037,
            6 => 1038, 7 => 1040, 8 => 1041, 9 => 1043, 10 => 1044,
            11 => 1045, 12 => 1047, 13 => 1048, 14 => 1049, 15 => 1050,
            16 => 1015, 17 => 1016, 18 => 1018, 19 => 1019, 20 => 1021,
            21 => 1022, 22 => 1024, 23 => 1025, 24 => 1026, 25 => 1027,
            26 => 1028, 27 => 1029, 28 => 1031, 29 => 1032, 30 => 1033,
        ];

        return $photoMap[$bannerId] ?? ($bannerId + 1000);
    }
}
