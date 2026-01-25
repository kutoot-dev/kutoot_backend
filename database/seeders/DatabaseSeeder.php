<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Nnjeim\World\World;

/**
 * DatabaseSeeder - Main seeder that calls all other seeders.
 *
 * DEV ONLY - Default login credentials:
 *
 * Admin Panel:
 *   - admin@kutoot.com / password (Super Admin)
 *   - admin2@kutoot.com / password (Admin)
 *
 * Seller Panel:
 *   - seller1 / 123456
 *   - seller2 / 123456
 *   - seller3 / 123456
 *
 * User/Customer:
 *   - visitor1@demo.com through visitor80@demo.com / 123456
 *
 * Note: All images are downloaded from Lorem Picsum and optimized as WebP.
 * Internet connection required for first-time seeding.
 *
 * Includes:
 *   - Campaign images (coin campaigns with banners)
 *   - Base plans with pricing tiers
 *   - User profile avatars
 *   - Master prizes with product images
 *   - Purchase orders with payment details
 *   - User coins credit/debit entries
 *   - Winners dummy data
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create test users first (required by other seeders)
        \App\Models\User::factory(10)->create();

        $this->call([
                // Core authentication seeders
            WorldSeeder::class,              // Countries, currencies, etc. from nnjeim/world
            AdminSeeder::class,
            EmailConfigurationSeeder::class, // Email/SMTP configuration
            SettingsSeeder::class,           // Logo, favicon, featured banner images
            ShopPageSeeder::class,           // Shop page settings with filter range
            StoreCategorySeeder::class,      // Store category images & icons
            SellerSeeder::class,
            ShopSeeder::class,
            MasterSettingsSeeder::class,
            ImageTypeSeeder::class,          // Image types (Banner, partners)

                // Product catalog seeders with HD images
            CategorySeeder::class,           // Product categories with logos, icons, images
            SubCategorySeeder::class,        // Sub-categories with images
            ChildCategorySeeder::class,      // Child categories with images
            BrandSeeder::class,              // Brand logos
            VendorSeeder::class,             // Vendor stores with banners
            ProductSeeder::class,            // Product thumbnails & gallery images

                // Homepage visual elements seeders
            HomePageVisibilitySeeder::class,
            SliderSeeder::class,             // Homepage carousel sliders
            BannerImageSeeder::class,        // Banner images
            StoreBannerSeeder::class,        // Responsive store banners
            SponsorSeeder::class,            // Sponsors and partners with logos
            ServiceSeeder::class,            // Service/feature icons
            TestimonialSeeder::class,        // Customer testimonial avatars

                // Coin and campaign system (Zoho-compatible global ledger)
            CoinCampaignSeeder::class,       // Campaign images
            BaseplanSeeder::class,           // Base plan pricing tiers with images
            MasterPrizeSeeder::class,        // Master prize catalog with images
            CoinLedgerSeeder::class,

                // Demo data (creates additional users with transactions)
            VisitorSeeder::class,
            TransactionSeeder::class,

                // User profile images
            UserProfileImageSeeder::class,   // User avatar images

                // E-commerce seeders
            CouponSeeder::class,             // Discount coupon codes
            FlashSaleSeeder::class,          // Flash sale events with products
            OrderSeeder::class,              // Sample orders with products
            WishlistSeeder::class,           // User wishlists
            ShoppingCartSeeder::class,       // Shopping cart items
            ProductReviewSeeder::class,      // Product reviews

                // Coin purchase and winner system
            PurchasedCoinsSeeder::class,     // Purchase orders with payment details
            UserCoinsSeeder::class,          // User coin credit/debit entries
            WinnersSeeder::class,            // Winners dummy data

                // Content seeders
            BlogCategorySeeder::class,       // Blog categories
            BlogSeeder::class,               // Blog posts with images
            BlogCommentSeeder::class,        // Blog post comments
            FaqSeeder::class,                // Frequently asked questions

                // SEO settings for all pages
            SeoSettingsSeeder::class,

                // Store applications seeder
            SellerApplicationSeeder::class,  // Store/seller applications

        ]);

        $this->command->info('');
        $this->command->info('=== All seeders completed successfully ===');
        $this->command->info('');
        $this->command->info('🖼️  HD images have been downloaded and optimized as WebP');
        $this->command->info('📁 Images saved to: public/uploads/');
        $this->command->info('');
        $this->command->info('Login Credentials (DEV ONLY):');
        $this->command->info('');
        $this->command->info('Admin Panel (/admin/login):');
        $this->command->info('  - admin@kutoot.com / password');
        $this->command->info('');
        $this->command->info('Seller Panel (/store/login):');
        $this->command->info('  - seller1 / 123456');
        $this->command->info('');
    }
}
