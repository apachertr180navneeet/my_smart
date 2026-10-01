<?php

namespace Database\Factories;

use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePackageFactory extends Factory
{
    protected $model = ServicePackage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph(),
            'provider_id' => null,
            'status' => 1,
            'price' => $this->faker->randomFloat(2, 50, 500),
            'is_featured' => 0,
            'package_type' => 'standard',
        ];
    }
}
