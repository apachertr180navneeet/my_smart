<?php

namespace Database\Factories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->word() . ' Tax',
            'type' => $this->faker->randomElement(['percentage', 'fixed']),
            'value' => $this->faker->randomFloat(2, 1, 30),
            'status' => 1,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
