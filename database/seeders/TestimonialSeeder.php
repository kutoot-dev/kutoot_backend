<?php

namespace Database\Seeders;

use App\Helpers\ImageSeederHelper;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * TestimonialSeeder - Seeds customer testimonials with HD avatar images.
 *
 * DEV ONLY: Creates sample testimonials with optimized WebP avatar images.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing testimonial avatars...');

        $testimonials = [
            [
                'name' => 'Sarah Johnson',
                'designation' => 'Marketing Director',
                'rating' => '5',
                'comment' => 'Absolutely love shopping here! The quality of products is exceptional and the delivery is always on time. Customer service is top-notch!',
                'picsum_id' => 64,
            ],
            [
                'name' => 'Michael Chen',
                'designation' => 'Software Engineer',
                'rating' => '5',
                'comment' => 'Best online shopping experience I\'ve ever had. The product descriptions are accurate and the prices are very competitive.',
                'picsum_id' => 91,
            ],
            [
                'name' => 'Emily Rodriguez',
                'designation' => 'Interior Designer',
                'rating' => '4',
                'comment' => 'Great selection of home decor items. Found exactly what I was looking for at a great price. Will definitely shop here again!',
                'picsum_id' => 177,
            ],
            [
                'name' => 'David Thompson',
                'designation' => 'Business Owner',
                'rating' => '5',
                'comment' => 'The quality exceeded my expectations. Fast shipping and secure packaging. This store has earned a loyal customer!',
                'picsum_id' => 175,
            ],
            [
                'name' => 'Lisa Patel',
                'designation' => 'Fitness Trainer',
                'rating' => '5',
                'comment' => 'Amazing products and even better customer service. They went above and beyond to help me find the right items.',
                'picsum_id' => 219,
            ],
            [
                'name' => 'Robert Wilson',
                'designation' => 'Photographer',
                'rating' => '4',
                'comment' => 'Very impressed with the product quality and packaging. The website is easy to navigate and checkout was smooth.',
                'picsum_id' => 334,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            // Download avatar image
            $imagePath = ImageSeederHelper::ensureImage(
                'testimonials',
                'testimonial-' . strtolower(str_replace(' ', '-', $testimonialData['name'])),
                'avatar',
                $testimonialData['picsum_id']
            );

            Testimonial::updateOrCreate(
                ['name' => $testimonialData['name']],
                [
                    'name' => $testimonialData['name'],
                    'designation' => $testimonialData['designation'],
                    'image' => $imagePath,
                    'rating' => $testimonialData['rating'],
                    'comment' => $testimonialData['comment'],
                    'status' => 1,
                ]
            );

            $this->command->line("  ✓ Testimonial: {$testimonialData['name']}");
        }

        $this->command->info('TestimonialSeeder completed. ' . count($testimonials) . ' testimonials seeded with HD avatars.');
    }
}
