<?php

namespace Database\Factories;

use App\Models\ServiceAddon;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceAddonFactory extends Factory
{
    protected $model = ServiceAddon::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'service_id' => Service::factory(),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'status' => 1,
            'created_by' => null,
        ];
    }
}
