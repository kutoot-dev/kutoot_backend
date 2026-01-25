<?php

namespace Database\Factories;

use App\Models\FlashSale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlashSaleFactory extends Factory
{
    protected $model = FlashSale::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'Mega Flash Sale', 'Weekend Bonanza', 'Lightning Deals',
            'Super Saver', 'Limited Time Offer', 'Clearance Sale'
        ]);

        return [
            'name' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'offer' => $this->faker->numberBetween(10, 50),
            'start_date' => now(),
            'end_date' => now()->addDays($this->faker->numberBetween(1, 7)),
            'status' => 1,
        ];
    }
}
