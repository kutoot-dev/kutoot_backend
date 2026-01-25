<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

/**
 * WishlistSeeder - Seeds user wishlists.
 *
 * DEV ONLY: Creates sample wishlists for testing.
 */
class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding wishlists...');

        $users = User::take(10)->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please run seeders first.');
            return;
        }

        $wishlistCount = 0;

        foreach ($users as $user) {
            // Each user gets 2-5 random products in wishlist
            $wishlistProducts = $products->random(rand(2, min(5, $products->count())));

            foreach ($wishlistProducts as $product) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
                $wishlistCount++;
            }
        }

        $this->command->info("WishlistSeeder completed. {$wishlistCount} wishlist items seeded.");
    }
}
