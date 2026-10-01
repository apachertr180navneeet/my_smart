<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'address' => $this->faker->address(),
            'lat' => $this->faker->latitude(),
            'long' => $this->faker->longitude(),
            'user_id' => User::factory(),
            'status' => 1,
        ];
    }
}
