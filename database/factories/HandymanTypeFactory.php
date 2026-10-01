<?php

namespace Database\Factories;

use App\Models\HandymanType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HandymanTypeFactory extends Factory
{
    protected $model = HandymanType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Handyman',
            'commission' => $this->faker->randomFloat(2, 5, 30),
            'status' => 1,
            'type' => $this->faker->randomElement(['full_time', 'part_time']),
        ];
    }
}
