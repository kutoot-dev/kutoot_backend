<?php

namespace Database\Seeders;

use App\Enums\BrandApprovalStatus;
use App\Helpers\ImageSeederHelper;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BrandSeeder - Seeds ecommerce brands with HD logo images.
 *
 * DEV ONLY: Creates sample brands with optimized WebP logos.
 */
class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Downloading and optimizing brand logos...');

        $brands = [
            ['name' => 'Apple', 'is_featured' => 1, 'is_top' => 1, 'picsum_id' => 866],
            ['name' => 'Samsung', 'is_featured' => 1, 'is_top' => 1, 'picsum_id' => 883],
            ['name' => 'Nike', 'is_featured' => 1, 'is_popular' => 1, 'picsum_id' => 890],
            ['name' => 'Adidas', 'is_featured' => 1, 'is_popular' => 1, 'picsum_id' => 906],
            ['name' => 'Sony', 'is_featured' => 1, 'is_trending' => 1, 'picsum_id' => 919],
            ['name' => 'LG', 'is_top' => 1, 'picsum_id' => 924],
            ['name' => 'Puma', 'is_popular' => 1, 'picsum_id' => 943],
            ['name' => 'Dell', 'is_trending' => 1, 'picsum_id' => 949],
            ['name' => 'HP', 'is_featured' => 1, 'picsum_id' => 984],
            ['name' => 'Lenovo', 'is_top' => 1, 'picsum_id' => 1026],
            ['name' => 'Microsoft', 'is_featured' => 1, 'is_top' => 1, 'picsum_id' => 1027],
            ['name' => 'Google', 'is_featured' => 1, 'is_trending' => 1, 'picsum_id' => 1028],
            ['name' => 'Amazon Basics', 'is_popular' => 1, 'picsum_id' => 1029],
            ['name' => 'Philips', 'is_top' => 1, 'picsum_id' => 1031],
            ['name' => 'Panasonic', 'picsum_id' => 1033],
            ['name' => 'Reebok', 'is_popular' => 1, 'picsum_id' => 1035],
            ['name' => 'Under Armour', 'is_trending' => 1, 'picsum_id' => 1037],
            ['name' => 'New Balance', 'picsum_id' => 1038],
            ['name' => 'Bose', 'is_featured' => 1, 'picsum_id' => 1040],
            ['name' => 'JBL', 'is_popular' => 1, 'picsum_id' => 1041],
        ];

        foreach ($brands as $brand) {
            // Download optimized brand logo
            $logoPath = ImageSeederHelper::ensureImage(
                'brands',
                'brand-' . Str::slug($brand['name']),
                'brand',
                $brand['picsum_id']
            );

            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'slug' => Str::slug($brand['name']),
                    'logo' => $logoPath,
                    'status' => 1,
                    'approval_status' => BrandApprovalStatus::APPROVED->value,
                    'is_featured' => $brand['is_featured'] ?? 0,
                    'is_top' => $brand['is_top'] ?? 0,
                    'is_popular' => $brand['is_popular'] ?? 0,
                    'is_trending' => $brand['is_trending'] ?? 0,
                ]
            );

            $this->command->line("  ✓ Brand: {$brand['name']}");
        }

        $this->command->info('BrandSeeder completed. ' . count($brands) . ' brands seeded with HD logos.');
    }
}
