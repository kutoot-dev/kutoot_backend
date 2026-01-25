<?php

namespace Database\Seeders;

use App\Enums\ProductApprovalStatus;
use App\Helpers\ImageSeederHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ProductSeeder - Seeds sample products with HD images and galleries.
 *
 * DEV ONLY: Creates sample products with optimized WebP thumbnail and gallery images.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing product images...');

        // Get first category, subcategory and brand for reference
        $category = Category::first();
        $subCategory = SubCategory::first();
        $brand = Brand::first();

        if (!$category) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'name' => 'Premium Wireless Headphones',
                'short_name' => 'Wireless Headphones',
                'price' => 149.99,
                'offer_price' => 129.99,
                'is_featured' => 1,
                'is_best' => 1,
                'picsum_ids' => [26, 27, 28, 29],
            ],
            [
                'name' => 'Smart Watch Pro Series',
                'short_name' => 'Smart Watch Pro',
                'price' => 299.99,
                'offer_price' => 249.99,
                'is_featured' => 1,
                'new_product' => 1,
                'picsum_ids' => [0, 1, 10, 20],
            ],
            [
                'name' => 'Portable Bluetooth Speaker',
                'short_name' => 'Bluetooth Speaker',
                'price' => 79.99,
                'offer_price' => null,
                'is_top' => 1,
                'picsum_ids' => [60, 42, 43, 48],
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'short_name' => 'Office Chair',
                'price' => 349.99,
                'offer_price' => 299.99,
                'is_best' => 1,
                'picsum_ids' => [36, 37, 38, 39],
            ],
            [
                'name' => 'Gaming Mechanical Keyboard',
                'short_name' => 'Gaming Keyboard',
                'price' => 129.99,
                'offer_price' => null,
                'new_product' => 1,
                'is_featured' => 1,
                'picsum_ids' => [180, 181, 182, 183],
            ],
            [
                'name' => 'Ultra HD 4K Monitor',
                'short_name' => '4K Monitor',
                'price' => 499.99,
                'offer_price' => 449.99,
                'is_top' => 1,
                'is_featured' => 1,
                'picsum_ids' => [2, 3, 4, 5],
            ],
            [
                'name' => 'Sports Running Shoes',
                'short_name' => 'Running Shoes',
                'price' => 89.99,
                'offer_price' => null,
                'is_best' => 1,
                'picsum_ids' => [21, 22, 23, 24],
            ],
            [
                'name' => 'Leather Wallet Premium',
                'short_name' => 'Leather Wallet',
                'price' => 59.99,
                'offer_price' => 49.99,
                'new_product' => 1,
                'picsum_ids' => [96, 97, 98, 99],
            ],
            [
                'name' => 'Stainless Steel Water Bottle',
                'short_name' => 'Water Bottle',
                'price' => 29.99,
                'offer_price' => null,
                'is_top' => 1,
                'picsum_ids' => [425, 426, 427, 428],
            ],
            [
                'name' => 'Wireless Charging Pad',
                'short_name' => 'Wireless Charger',
                'price' => 39.99,
                'offer_price' => 29.99,
                'is_featured' => 1,
                'picsum_ids' => [160, 161, 162, 163],
            ],
            [
                'name' => 'Noise Cancelling Earbuds',
                'short_name' => 'NC Earbuds',
                'price' => 199.99,
                'offer_price' => 179.99,
                'is_best' => 1,
                'new_product' => 1,
                'picsum_ids' => [250, 251, 252, 253],
            ],
            [
                'name' => 'Laptop Backpack Professional',
                'short_name' => 'Laptop Backpack',
                'price' => 79.99,
                'offer_price' => null,
                'is_featured' => 1,
                'picsum_ids' => [320, 321, 322, 323],
            ],
        ];

        foreach ($products as $index => $productData) {
            $slug = Str::slug($productData['name']);

            // Download thumbnail image
            $thumbPath = ImageSeederHelper::ensureImage(
                'products',
                'product-' . $slug,
                'product',
                $productData['picsum_ids'][0]
            );

            // Create product
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $productData['name'],
                    'short_name' => $productData['short_name'],
                    'slug' => $slug,
                    'thumb_image' => $thumbPath,
                    'category_id' => $category->id,
                    'sub_category_id' => $subCategory?->id ?? 0,
                    'child_category_id' => 0,
                    'brand_id' => $brand?->id ?? 0,
                    'qty' => rand(50, 200),
                    'stock' => rand(20, 100),
                    'sold_qty' => rand(10, 50),
                    'short_description' => 'High-quality ' . $productData['short_name'] . ' with premium features and excellent durability.',
                    'long_description' => '<p>Introducing the ' . $productData['name'] . ' - a premium product designed for those who demand excellence.</p><p>Features include:</p><ul><li>Premium build quality</li><li>Extended warranty</li><li>Fast shipping available</li><li>30-day return policy</li></ul><p>Perfect for everyday use with exceptional performance.</p>',
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'seo_title' => $productData['name'] . ' | Best Price Online',
                    'seo_description' => 'Buy ' . $productData['name'] . ' at the best price. Free shipping on orders over $50.',
                    'price' => $productData['price'],
                    'offer_price' => $productData['offer_price'],
                    'offer_start_date' => $productData['offer_price'] ? now() : null,
                    'offer_end_date' => $productData['offer_price'] ? now()->addMonths(3) : null,
                    'is_cash_delivery' => 1,
                    'is_return' => 1,
                    'show_homepage' => 1,
                    'is_featured' => $productData['is_featured'] ?? 0,
                    'new_product' => $productData['new_product'] ?? 0,
                    'is_top' => $productData['is_top'] ?? 0,
                    'is_best' => $productData['is_best'] ?? 0,
                    'status' => 1,
                    'approval_status' => ProductApprovalStatus::APPROVED->value,
                ]
            );

            $this->command->line("  ✓ Product: {$productData['name']}");

            // Create gallery images (skip first one as it's the thumbnail)
            $galleryPicsumIds = array_slice($productData['picsum_ids'], 1);

            // Delete existing gallery images for this product
            ProductGallery::where('product_id', $product->id)->delete();

            foreach ($galleryPicsumIds as $i => $picsumId) {
                $galleryPath = ImageSeederHelper::ensureImage(
                    'product-galleries',
                    'gallery-' . $slug . '-' . ($i + 1),
                    'gallery',
                    $picsumId
                );

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image' => $galleryPath,
                    'status' => 1,
                ]);
            }

            $this->command->line("    + " . count($galleryPicsumIds) . " gallery images");
        }

        $this->command->info('ProductSeeder completed. ' . count($products) . ' products seeded with HD images and galleries.');
    }
}
