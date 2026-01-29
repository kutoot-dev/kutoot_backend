<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * ProductReviewSeeder - Seeds product reviews.
 *
 * DEV ONLY: Creates sample product reviews for testing.
 */
class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding product reviews...');

        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please run seeders first.');
            return;
        }

        $reviews = [
            ['rating' => 5, 'review' => 'Excellent product! Exactly what I was looking for. Fast shipping and great quality.'],
            ['rating' => 5, 'review' => 'Amazing quality and value for money. Highly recommended!'],
            ['rating' => 4, 'review' => 'Very good product. Minor improvements needed but overall satisfied.'],
            ['rating' => 4, 'review' => 'Great product, fast delivery. Would buy again.'],
            ['rating' => 5, 'review' => 'Perfect! Exceeded my expectations in every way.'],
            ['rating' => 3, 'review' => 'Decent product for the price. Does what it is supposed to do.'],
            ['rating' => 4, 'review' => 'Good quality, nice packaging. Happy with my purchase.'],
            ['rating' => 5, 'review' => 'Best purchase I have made this year. Outstanding quality!'],
            ['rating' => 4, 'review' => 'Really impressed with the build quality. Recommended.'],
            ['rating' => 5, 'review' => 'Love it! The product arrived on time and works perfectly.'],
        ];

        $reviewCount = 0;
        $vendor = Vendor::first();

        foreach ($products->take(12) as $product) {
            // Each product gets 2-4 reviews
            $numReviews = rand(2, 4);

            for ($i = 0; $i < $numReviews; $i++) {
                $user = $users->random();
                $reviewData = $reviews[array_rand($reviews)];

                ProductReview::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'product_vendor_id' => $vendor?->id ?? 1,
                        'rating' => $reviewData['rating'],
                        'review' => $reviewData['review'],
                        'status' => 1,
                    ]
                );
                $reviewCount++;
            }
        }

        $this->command->info("ProductReviewSeeder completed. {$reviewCount} reviews seeded.");
    }
}
