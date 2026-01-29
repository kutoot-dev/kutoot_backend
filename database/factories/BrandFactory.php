<?php

namespace Database\Factories;

use App\Enums\BrandApprovalStatus;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo' => 'https://picsum.photos/seed/' . Str::slug($name) . '/200/200',
            'status' => 1,
            'approval_status' => BrandApprovalStatus::APPROVED->value,
            'is_featured' => $this->faker->boolean(30),
            'is_top' => $this->faker->boolean(20),
            'is_popular' => $this->faker->boolean(20),
            'is_trending' => $this->faker->boolean(20),
        ];
    }
}
