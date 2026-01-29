<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * FlashSaleSeeder - Seeds flash sale events with products.
 *
 * DEV ONLY: Creates sample flash sales with discounted products.
 */
class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding flash sales...');

        $flashSales = [
            [
                'name' => 'Weekend Mega Sale',
                'offer' => 30,
                'start_date' => now(),
                'end_date' => now()->addDays(3),
                'status' => 1,
            ],
            [
                'name' => 'Electronics Clearance',
                'offer' => 40,
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'status' => 1,
            ],
            [
                'name' => '24-Hour Flash Deal',
                'offer' => 50,
                'start_date' => now(),
                'end_date' => now()->addDay(),
                'status' => 1,
            ],
        ];

        $products = Product::inRandomOrder()->take(12)->get();

        foreach ($flashSales as $index => $saleData) {
            $slug = Str::slug($saleData['name']) . '-' . ($index + 1);

            $flashSale = FlashSale::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $saleData['name'],
                    'slug' => $slug,
                    'offer' => $saleData['offer'],
                    'start_date' => $saleData['start_date'],
                    'end_date' => $saleData['end_date'],
                    'status' => $saleData['status'],
                ]
            );

            $this->command->line("  ✓ Flash Sale: {$saleData['name']}");

            // Add random products to this flash sale
            if ($products->count() > 0) {
                $saleProducts = $products->slice($index * 4, 4);

                foreach ($saleProducts as $product) {
                    FlashSaleProduct::updateOrCreate(
                        ['flash_sale_id' => $flashSale->id, 'product_id' => $product->id],
                        [
                            'flash_sale_id' => $flashSale->id,
                            'product_id' => $product->id,
                            'status' => 1,
                            'show_homepage' => 1,
                        ]
                    );
                }

                $this->command->line("    + Added " . $saleProducts->count() . " products");
            }
        }

        $this->command->info('FlashSaleSeeder completed. ' . count($flashSales) . ' flash sales seeded.');
    }
}
