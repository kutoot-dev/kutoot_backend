<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoSetting;

/**
 * SeoSettingsSeeder - Seeds default SEO settings for all pages
 */
class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'id' => 1,
                'page_name' => 'Home Page',
                'seo_title' => 'Home - Welcome to Shop',
                'seo_description' => 'A best ecommerce script',
            ],
            [
                'id' => 2,
                'page_name' => 'About Us',
                'seo_title' => 'About Us - Our Story',
                'seo_description' => 'Learn more about our company and mission',
            ],
            [
                'id' => 3,
                'page_name' => 'Contact Us',
                'seo_title' => 'Contact Us - Get in Touch',
                'seo_description' => 'Contact us for any inquiries or support',
            ],
            [
                'id' => 4,
                'page_name' => 'Seller Page',
                'seo_title' => 'Become a Seller',
                'seo_description' => 'Join our marketplace as a seller',
            ],
            [
                'id' => 5,
                'page_name' => 'Flash Deal',
                'seo_title' => 'Flash Deals - Limited Time Offers',
                'seo_description' => 'Grab amazing deals before they expire',
            ],
            [
                'id' => 6,
                'page_name' => 'Blog',
                'seo_title' => 'Blog - Latest News & Updates',
                'seo_description' => 'Read our latest articles and updates',
            ],
            [
                'id' => 7,
                'page_name' => 'Shop Page',
                'seo_title' => 'Shop - Browse All Products',
                'seo_description' => 'Explore our wide range of products',
            ],
            [
                'id' => 8,
                'page_name' => 'Dashboard',
                'seo_title' => 'Dashboard - Manage Your Account',
                'seo_description' => 'Access your account dashboard',
            ],
        ];

        foreach ($pages as $page) {
            SeoSetting::updateOrCreate(
                ['id' => $page['id']],
                $page
            );
        }

        $this->command->info('SeoSettingsSeeder completed. ' . count($pages) . ' page SEO settings created.');
    }
}
