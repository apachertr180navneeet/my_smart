<?php

namespace Database\Factories;

use App\Models\ProviderType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderTypeFactory extends Factory
{
    protected $model = ProviderType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Provider',
            'commission' => $this->faker->randomFloat(2, 5, 30),
            'status' => 1,
            'type' => $this->faker->randomElement(['individual', 'company']),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
