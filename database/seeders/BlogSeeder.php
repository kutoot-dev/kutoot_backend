<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BlogSeeder - Seeds blog posts with HD images.
 *
 * DEV ONLY: Creates sample blog posts with optimized WebP images.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing blog images...');

        $admin = Admin::first();

        if (!$admin) {
            $this->command->warn('No admin found. Please run AdminSeeder first.');
            return;
        }

        $blogs = [
            [
                'title' => '10 Must-Have Gadgets for 2026',
                'category' => 'Technology',
                'description' => '<p>Discover the top gadgets that are revolutionizing the way we live, work, and play. From AI-powered devices to sustainable tech, here are our picks for the must-have gadgets of 2026.</p><p>Technology continues to evolve at a rapid pace, bringing us innovative solutions that make our lives easier and more connected.</p><h3>1. Smart Home Hub Pro</h3><p>Control your entire home with voice commands and intelligent automation.</p>',
                'is_popular' => 1,
                'show_homepage' => 1,
                'picsum_id' => 0,
            ],
            [
                'title' => 'The Ultimate Guide to Online Shopping',
                'category' => 'Shopping Tips',
                'description' => '<p>Make smarter purchasing decisions with our comprehensive guide to online shopping. Learn how to find the best deals, avoid scams, and shop securely.</p><h3>Compare Prices</h3><p>Always compare prices across multiple platforms before making a purchase.</p>',
                'is_popular' => 0,
                'show_homepage' => 1,
                'picsum_id' => 1080,
            ],
            [
                'title' => 'Sustainable Fashion: Eco-Friendly Trends',
                'category' => 'Fashion Tips',
                'description' => '<p>Explore the latest in sustainable fashion and learn how to build an eco-conscious wardrobe without compromising on style.</p><h3>Quality Over Quantity</h3><p>Invest in timeless pieces that last longer rather than fast fashion items.</p>',
                'is_popular' => 1,
                'show_homepage' => 0,
                'picsum_id' => 669,
            ],
            [
                'title' => '5 Tips for a Healthier Lifestyle',
                'category' => 'Health & Wellness',
                'description' => '<p>Small changes can make a big difference in your overall health and wellbeing. Here are five simple tips to help you live a healthier life.</p><h3>1. Stay Hydrated</h3><p>Drink at least 8 glasses of water daily.</p>',
                'is_popular' => 0,
                'show_homepage' => 1,
                'picsum_id' => 177,
            ],
            [
                'title' => 'Product Review: Smart Watch Pro 2026',
                'category' => 'Product Reviews',
                'description' => '<p>An in-depth review of the Smart Watch Pro 2026—features, performance, battery life, and whether it is worth the investment.</p><h3>Design & Build</h3><p>Premium materials with a sleek, modern design.</p>',
                'is_popular' => 1,
                'show_homepage' => 0,
                'picsum_id' => 26,
            ],
            [
                'title' => 'How to Choose the Perfect Gift',
                'category' => 'How-To Guides',
                'description' => '<p>Finding the right gift can be challenging. Use our comprehensive guide to select thoughtful presents for any occasion.</p><h3>Consider Their Interests</h3><p>Pay attention to what they talk about and their hobbies.</p>',
                'is_popular' => 0,
                'show_homepage' => 1,
                'picsum_id' => 452,
            ],
            [
                'title' => 'Home Office Setup: Essential Equipment',
                'category' => 'Lifestyle',
                'description' => '<p>Create the perfect work-from-home environment with our guide to essential home office equipment and setup tips.</p><h3>Ergonomic Chair</h3><p>Invest in a quality chair with good lumbar support.</p>',
                'is_popular' => 1,
                'show_homepage' => 1,
                'picsum_id' => 180,
            ],
            [
                'title' => 'Latest Updates: New Features Rolling Out',
                'category' => 'News & Updates',
                'description' => '<p>Stay informed about the latest features and improvements we are bringing to enhance your shopping experience.</p><h3>Improved Search</h3><p>Find products faster with our enhanced search algorithm.</p>',
                'is_popular' => 0,
                'show_homepage' => 0,
                'picsum_id' => 1031,
            ],
        ];

        foreach ($blogs as $blogData) {
            $blogCategory = BlogCategory::where('name', $blogData['category'])->first();

            if (!$blogCategory) {
                $blogCategory = BlogCategory::create([
                    'name' => $blogData['category'],
                    'slug' => Str::slug($blogData['category']),
                    'status' => 1,
                ]);
            }

            $slug = Str::slug($blogData['title']);

            // Download blog image
            $imagePath = ImageSeederHelper::ensureImage(
                'blogs',
                'blog-' . $slug,
                'banner',
                $blogData['picsum_id']
            );

            Blog::updateOrCreate(
                ['slug' => $slug],
                [
                    'blog_category_id' => $blogCategory->id,
                    'admin_id' => $admin->id,
                    'title' => $blogData['title'],
                    'slug' => $slug,
                    'description' => $blogData['description'],
                    'image' => $imagePath,
                    'views' => rand(100, 5000),
                    'seo_title' => $blogData['title'],
                    'seo_description' => strip_tags(Str::limit($blogData['description'], 150)),
                    'status' => 1,
                    'is_popular' => $blogData['is_popular'],
                    'show_homepage' => $blogData['show_homepage'],
                ]
            );

            $this->command->line("  ✓ Blog: {$blogData['title']}");
        }

        $this->command->info('BlogSeeder completed. ' . count($blogs) . ' blog posts seeded with HD images.');
    }
}
