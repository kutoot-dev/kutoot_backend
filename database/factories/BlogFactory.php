<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'blog_category_id' => BlogCategory::inRandomOrder()->first()?->id ?? BlogCategory::factory(),
            'admin_id' => Admin::inRandomOrder()->first()?->id ?? 1,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>',
            'image' => 'https://picsum.photos/seed/' . Str::slug($title) . '/1200/600',
            'views' => $this->faker->numberBetween(0, 5000),
            'seo_title' => $title,
            'seo_description' => $this->faker->sentence(15),
            'status' => 1,
            'is_popular' => $this->faker->boolean(20),
            'show_homepage' => $this->faker->boolean(30),
        ];
    }
}
