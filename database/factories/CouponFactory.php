<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('???####')),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount' => $this->faker->randomFloat(2, 5, 50),
            'expire_date' => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            'status' => 1,
            'type' => 'all',
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expire_date' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
