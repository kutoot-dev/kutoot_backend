<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Models\Setting;

/**
 * SettingsSeeder - Dev only
 * Seeds default application settings required for homepage with optimized images
 */
class SettingsSeeder extends Seeder
{
    /**
     * Path to the Kutoot brand logo source file
     */
    protected string $brandLogoSource = 'assets/brand/kutoot-logo.png';

    public function run()
    {
        $this->command->info('Creating and optimizing settings images...');

        // Use Kutoot brand logo for logo, favicon, and login image
        $logoPath = $this->copyBrandLogo('logo', 300, 100);
        $this->command->line('  ✓ Kutoot logo created');

        $faviconPath = $this->copyBrandLogo('favicon', 32, 32);
        $this->command->line('  ✓ Kutoot favicon created');

        $loginImagePath = $this->copyBrandLogo('login-image', 600, 400);
        $this->command->line('  ✓ Kutoot login image created');

        // Generate featured category banner
        $featuredBannerPath = ImageSeederHelper::ensureImage('website-images', 'featured-category', 'banner', 1015);
        $this->command->line('  ✓ Featured category banner created');

        Setting::updateOrCreate(
            ['id' => 1],
            [
                'logo' => $logoPath,
                'favicon' => $faviconPath,
                'enable_user_register' => 1,
                'enable_multivendor' => 1,
                'enable_subscription_notify' => 1,
                'enable_save_contact_message' => 1,
                'text_direction' => 'LTR',
                'timezone' => 'Asia/Kolkata',
                'sidebar_lg_header' => 'Kutoot',
                'sidebar_sm_header' => 'K',
                'featured_category_banner' => $featuredBannerPath,
                'current_version' => '7.7',
                'tax' => 18.0,
                'currency_id' => 1,
                'home_section_title' => [
                    'featured_categories' => 'Featured Categories',
                    'popular_products' => 'Popular Products',
                    'new_arrivals' => 'New Arrivals',
                    'best_sellers' => 'Best Sellers',
                    'trending_now' => 'Trending Now',
                ],
                'homepage_section_title' => json_encode([
                    ['key' => 'slider', 'default' => 'Top Deals', 'custom' => 'Top Deals'],
                    ['key' => 'service', 'default' => 'Our Services', 'custom' => 'Our Services'],
                    ['key' => 'popular_category', 'default' => 'Popular Categories', 'custom' => 'Popular Categories'],
                    ['key' => 'flash_sale', 'default' => 'Flash Sale', 'custom' => 'Flash Sale'],
                    ['key' => 'top_rated', 'default' => 'Top Rated Products', 'custom' => 'Top Rated Products'],
                    ['key' => 'seller', 'default' => 'Featured Sellers', 'custom' => 'Featured Sellers'],
                    ['key' => 'featured_category', 'default' => 'Featured Products', 'custom' => 'Featured Products'],
                    ['key' => 'new_arrival', 'default' => 'New Arrivals', 'custom' => 'New Arrivals'],
                    ['key' => 'best_product', 'default' => 'Best Products', 'custom' => 'Best Products'],
                ]),
                'popular_category_banner' => $featuredBannerPath,
                'contact_email' => 'contact@kutoot.com',
                'topbar_phone' => '+91 1234567890',
                'topbar_email' => 'support@kutoot.com',
                'show_product_progressbar' => 1,
                'phone_number_required' => 'optional',
                'default_phone_code' => '+91',
                'theme_one' => '#FF6B6B',
                'theme_two' => '#4ECDC4',
                'currency_icon' => '₹',
                'currency_name' => 'INR',
                'seller_condition' => 'By registering as a seller, you agree to our terms and conditions.',
                'empty_cart' => null,
                'empty_wishlist' => null,
                'change_password_image' => null,
                'become_seller_avatar' => null,
                'become_seller_banner' => null,
                'login_image' => $loginImagePath,
                'error_page' => null,
            ]
        );

        $this->command->info('SettingsSeeder completed. Default settings created with Kutoot branding.');
    }

    /**
     * Copy and resize the Kutoot brand logo for different uses
     *
     * @param string $prefix Filename prefix (logo, favicon, login-image)
     * @param int $width Target width
     * @param int $height Target height
     * @return string Relative path to the saved image
     */
    protected function copyBrandLogo(string $prefix, int $width, int $height): string
    {
        $sourcePath = public_path($this->brandLogoSource);
        $targetDir = public_path('uploads/website-images');
        $filename = $prefix . '-kutoot-' . date('Y-m-d-H-i-s') . '.png';
        $relativePath = 'uploads/website-images/' . $filename;
        $targetPath = $targetDir . '/' . $filename;

        // Ensure target directory exists
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        // Check if brand logo source exists
        if (File::exists($sourcePath)) {
            // Resize and save the brand logo
            $image = Image::make($sourcePath);
            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // For favicon, ensure it's exactly the right size with padding
            if ($prefix === 'favicon') {
                $canvas = Image::canvas($width, $height, null);
                $canvas->insert($image, 'center');
                $canvas->save($targetPath);
            } else {
                $image->save($targetPath);
            }

            return $relativePath;
        }

        // Fallback: Create a placeholder with Kutoot branding colors
        $this->command->warn("  ⚠ Brand logo not found at {$this->brandLogoSource}. Creating placeholder...");
        return $this->createKutootPlaceholder($prefix, $width, $height);
    }

    /**
     * Create a Kutoot-branded placeholder image
     */
    protected function createKutootPlaceholder(string $prefix, int $width, int $height): string
    {
        $targetDir = public_path('uploads/website-images');
        $filename = $prefix . '-kutoot-placeholder-' . date('Y-m-d-H-i-s') . '.png';
        $relativePath = 'uploads/website-images/' . $filename;
        $targetPath = $targetDir . '/' . $filename;

        // Ensure target directory exists
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        // Create canvas with Kutoot brand color (orange/maroon gradient look)
        $image = Image::canvas($width, $height, '#B22234');

        // Add Kutoot text
        $fontSize = min($width, $height) / 4;
        $image->text('KUTOOT', $width / 2, $height / 2, function ($font) use ($fontSize) {
            $font->size($fontSize);
            $font->color('#FF8C00');
            $font->align('center');
            $font->valign('middle');
        });

        $image->save($targetPath);

        return $relativePath;
    }
}
