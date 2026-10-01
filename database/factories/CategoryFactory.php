<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'is_featured' => $this->faker->randomElement([0, 1]),
            'status' => 1,
            'color' => $this->faker->hexColor(),
            'slug' => $this->faker->unique()->slug(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => 1]);
    }
}
