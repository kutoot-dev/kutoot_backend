<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

/**
 * CouponSeeder - Seeds discount coupons.
 *
 * DEV ONLY: Creates sample coupon codes for testing checkout discounts.
 */
class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding coupons...');

        $coupons = [
            [
                'name' => 'New Customer Discount',
                'code' => 'WELCOME10',
                'discount' => 10,
                'offer_type' => 1, // Percentage
                'min_purchase_price' => 50,
                'max_discount' => 25,
                'number_of_time' => 1000,
            ],
            [
                'name' => 'Summer Sale',
                'code' => 'SUMMER25',
                'discount' => 25,
                'offer_type' => 1, // Percentage
                'min_purchase_price' => 100,
                'max_discount' => 50,
                'number_of_time' => 500,
            ],
            [
                'name' => 'Flat $15 Off',
                'code' => 'FLAT15',
                'discount' => 15,
                'offer_type' => 2, // Fixed amount
                'min_purchase_price' => 75,
                'max_discount' => null,
                'number_of_time' => 300,
            ],
            [
                'name' => 'Weekend Special',
                'code' => 'WEEKEND20',
                'discount' => 20,
                'offer_type' => 1, // Percentage
                'min_purchase_price' => 80,
                'max_discount' => 40,
                'number_of_time' => 200,
            ],
            [
                'name' => 'VIP Member Discount',
                'code' => 'VIP30',
                'discount' => 30,
                'offer_type' => 1, // Percentage
                'min_purchase_price' => 150,
                'max_discount' => 75,
                'number_of_time' => 100,
            ],
            [
                'name' => 'Free Shipping',
                'code' => 'FREESHIP',
                'discount' => 10,
                'offer_type' => 2, // Fixed amount (shipping cost)
                'min_purchase_price' => 50,
                'max_discount' => null,
                'number_of_time' => 1000,
            ],
            [
                'name' => 'Flash Sale',
                'code' => 'FLASH50',
                'discount' => 50,
                'offer_type' => 2, // Fixed amount
                'min_purchase_price' => 200,
                'max_discount' => null,
                'number_of_time' => 50,
            ],
            [
                'name' => 'Holiday Special',
                'code' => 'HOLIDAY15',
                'discount' => 15,
                'offer_type' => 1, // Percentage
                'min_purchase_price' => 60,
                'max_discount' => 30,
                'number_of_time' => 400,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::updateOrCreate(
                ['code' => $couponData['code']],
                [
                    'name' => $couponData['name'],
                    'code' => $couponData['code'],
                    'discount' => $couponData['discount'],
                    'offer_type' => $couponData['offer_type'],
                    'min_purchase_price' => $couponData['min_purchase_price'],
                    'max_discount' => $couponData['max_discount'],
                    'number_of_time' => $couponData['number_of_time'],
                    'applied_qty' => 0,
                    'start_date' => now(),
                    'end_date' => now()->addMonths(6),
                    'status' => 1,
                ]
            );

            $this->command->line("  ✓ Coupon: {$couponData['code']} - {$couponData['name']}");
        }

        $this->command->info('CouponSeeder completed. ' . count($coupons) . ' coupons seeded.');
    }
}
