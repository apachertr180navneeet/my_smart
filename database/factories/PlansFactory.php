<?php

namespace Database\Factories;

use App\Models\Plans;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlansFactory extends Factory
{
    protected $model = Plans::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->word() . ' Plan',
            'identifier' => $this->faker->unique()->slug(),
            'type' => 'subscription',
            'amount' => $this->faker->randomFloat(2, 10, 200),
            'status' => 1,
            'duration' => $this->faker->randomElement([30, 90, 180, 365]),
            'description' => $this->faker->sentence(),
            'trial_period' => 0,
            'plan_type' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
