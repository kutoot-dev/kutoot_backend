<?php

namespace Database\Factories;

use App\Enums\ProductApprovalStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $productNames = [
            'Wireless Headphones', 'Smart Watch', 'Bluetooth Speaker', 'USB-C Hub',
            'Mechanical Keyboard', 'Gaming Mouse', 'LED Monitor', 'Laptop Stand',
            'Webcam HD', 'Portable Charger', 'Phone Case', 'Screen Protector',
            'Tablet Cover', 'Earbuds Pro', 'Desk Lamp', 'Office Chair',
            'Standing Desk', 'Monitor Arm', 'Cable Organizer', 'Mouse Pad',
            'Leather Wallet', 'Travel Bag', 'Water Bottle', 'Coffee Mug',
            'Running Shoes', 'Sports Jersey', 'Yoga Mat', 'Fitness Tracker',
            'Sunglasses', 'Watch Band', 'Bracelet', 'Ring Set',
        ];

        $name = $this->faker->randomElement($productNames) . ' ' . $this->faker->randomElement(['Pro', 'Max', 'Plus', 'Ultra', 'Lite', 'Elite', 'Premium', '2026']);
        $price = $this->faker->randomFloat(2, 19.99, 499.99);
        $hasOffer = $this->faker->boolean(40);

        return [
            'name' => $name,
            'short_name' => Str::limit($name, 20, ''),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'thumb_image' => 'https://picsum.photos/seed/' . Str::slug($name) . '/800/800',
            'vendor_id' => Vendor::inRandomOrder()->first()?->id ?? 1,
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'sub_category_id' => SubCategory::inRandomOrder()->first()?->id ?? 0,
            'child_category_id' => 0,
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? 0,
            'qty' => $this->faker->numberBetween(50, 500),
            'stock' => $this->faker->numberBetween(10, 200),
            'sold_qty' => $this->faker->numberBetween(0, 100),
            'short_description' => $this->faker->paragraph(1),
            'long_description' => '<p>' . $this->faker->paragraph(3) . '</p><ul><li>' . implode('</li><li>', $this->faker->sentences(4)) . '</li></ul>',
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'seo_title' => $name . ' | Best Price',
            'seo_description' => 'Buy ' . $name . ' at the best price with free shipping.',
            'price' => $price,
            'offer_price' => $hasOffer ? round($price * 0.8, 2) : null,
            'offer_start_date' => $hasOffer ? now() : null,
            'offer_end_date' => $hasOffer ? now()->addMonths(2) : null,
            'is_cash_delivery' => 1,
            'is_return' => 1,
            'show_homepage' => $this->faker->boolean(50),
            'is_featured' => $this->faker->boolean(30),
            'new_product' => $this->faker->boolean(30),
            'is_top' => $this->faker->boolean(20),
            'is_best' => $this->faker->boolean(20),
            'status' => 1,
            'approval_status' => ProductApprovalStatus::APPROVED->value,
        ];
    }
}
