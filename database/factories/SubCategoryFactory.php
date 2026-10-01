<?php

namespace Database\Factories;

use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'is_featured' => $this->faker->randomElement([0, 1]),
            'status' => 1,
            'category_id' => Category::factory(),
            'slug' => $this->faker->unique()->slug(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
