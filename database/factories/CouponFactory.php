<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $isPercentage = $this->faker->boolean(60);

        return [
            'name' => $this->faker->randomElement(['Summer Sale', 'Flash Deal', 'New Customer', 'Weekend Special', 'Holiday Offer', 'Member Exclusive']),
            'code' => strtoupper(Str::random(8)),
            'number_of_time' => $this->faker->numberBetween(10, 500),
            'applied_qty' => $this->faker->numberBetween(0, 50),
            'min_purchase_price' => $this->faker->randomFloat(2, 50, 200),
            'max_discount' => $isPercentage ? $this->faker->randomFloat(2, 20, 100) : null,
            'offer_type' => $isPercentage ? 1 : 2, // 1 = percentage, 2 = fixed
            'discount' => $isPercentage ? $this->faker->numberBetween(5, 30) : $this->faker->randomFloat(2, 10, 50),
            'start_date' => now(),
            'end_date' => now()->addMonths($this->faker->numberBetween(1, 6)),
            'status' => 1,
        ];
    }
}
