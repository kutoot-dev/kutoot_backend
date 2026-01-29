<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Technology', 'Lifestyle', 'Fashion Tips', 'Health & Wellness',
            'Product Reviews', 'How-To Guides', 'News & Updates', 'Trends',
            'Shopping Tips', 'Deals & Offers', 'Behind the Scenes', 'Interviews'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => 1,
        ];
    }
}
