<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'category_id' => Category::factory(),
            'provider_id' => null,
            'type' => $this->faker->randomElement(['fixed', 'hourly']),
            'is_slot' => 0,
            'discount' => 0,
            'duration' => $this->faker->randomElement(['1 hour', '2 hours', '30 minutes']),
            'description' => $this->faker->paragraph(),
            'is_featured' => 0,
            'status' => 1,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'added_by' => null,
            'service_type' => 'service',
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

    public function hourly(): static
    {
        return $this->state(fn () => ['type' => 'hourly']);
    }
}
