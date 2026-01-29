<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $orderStatuses = ['PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED'];
        $paymentStatuses = ['pending', 'success', 'failed'];
        $paymentTypes = ['COD', 'Online', 'Wallet', 'Card'];

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'order_id' => 'ORD-' . strtoupper($this->faker->unique()->bothify('??######')),
            'product_qty' => $this->faker->numberBetween(1, 5),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'sub_total' => $this->faker->randomFloat(2, 40, 900),
            'amount_real_currency' => $this->faker->randomFloat(2, 50, 1000),
            'amount_usd_currency' => $this->faker->randomFloat(2, 50, 1000),
            'coupon_discount' => $this->faker->boolean(30) ? $this->faker->randomFloat(2, 5, 50) : 0,
            'shipping_cost' => $this->faker->randomFloat(2, 0, 20),
            'tax' => $this->faker->randomFloat(2, 0, 50),
            'order_status' => $this->faker->randomElement($orderStatuses),
            'payment_status' => $this->faker->randomElement($paymentStatuses),
            'payment_type' => $this->faker->randomElement($paymentTypes),
            'order_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'order_month' => $this->faker->numberBetween(1, 12),
            'order_year' => $this->faker->numberBetween(2024, 2026),
            'additional_info' => $this->faker->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'PENDING',
            'payment_status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'CONFIRMED',
            'payment_status' => 'success',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'DELIVERED',
            'payment_status' => 'success',
        ]);
    }
}
