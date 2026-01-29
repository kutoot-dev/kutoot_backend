<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ShoppingCartSeeder - Seeds shopping cart items.
 *
 * DEV ONLY: Creates sample cart items for testing checkout.
 */
class ShoppingCartSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding shopping carts...');

        $users = User::take(5)->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please run seeders first.');
            return;
        }

        $cartCount = 0;

        foreach ($users as $user) {
            // Each user gets 1-3 products in cart
            $cartProducts = $products->random(rand(1, min(3, $products->count())));

            foreach ($cartProducts as $product) {
                $qty = rand(1, 3);

                ShoppingCart::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'total_price' => $product->price * $qty,
                    ]
                );
                $cartCount++;
            }
        }

        $this->command->info("ShoppingCartSeeder completed. {$cartCount} cart items seeded.");
    }
}
