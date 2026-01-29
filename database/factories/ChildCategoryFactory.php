<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChildCategoryFactory extends Factory
{
    protected $model = ChildCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        $subCategory = SubCategory::inRandomOrder()->first() ?? SubCategory::factory()->create();

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'category_id' => $subCategory->category_id,
            'sub_category_id' => $subCategory->id,
            'status' => 1,
        ];
    }
}
