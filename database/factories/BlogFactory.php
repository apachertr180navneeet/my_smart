<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(),
            'description' => $this->faker->paragraph(),
            'total_views' => $this->faker->numberBetween(0, 10000),
            'author_id' => User::factory(),
            'status' => 1,
            'is_featured' => 0,
            'tags' => $this->faker->words(3, true),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
