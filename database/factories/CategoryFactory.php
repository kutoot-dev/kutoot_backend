<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Electronics', 'Fashion', 'Home & Living', 'Sports & Fitness',
            'Beauty & Health', 'Books & Stationery', 'Toys & Games',
            'Jewelry & Watches', 'Automotive', 'Grocery', 'Garden',
            'Pet Supplies', 'Office Products', 'Baby & Kids', 'Music'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'logo' => 'https://picsum.photos/seed/' . Str::slug($name) . '-logo/400/400',
            'icon' => 'https://picsum.photos/seed/' . Str::slug($name) . '-icon/100/100',
            'image' => 'https://picsum.photos/seed/' . Str::slug($name) . '/400/400',
            'status' => 1,
            'is_featured' => $this->faker->boolean(30),
            'is_top' => $this->faker->boolean(20),
            'is_popular' => $this->faker->boolean(20),
            'is_trending' => $this->faker->boolean(20),
        ];
    }
}
