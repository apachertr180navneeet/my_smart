<?php

namespace Database\Factories;

use App\Models\HelpDesk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HelpDeskFactory extends Factory
{
    protected $model = HelpDesk::class;

    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(),
            'employee_id' => User::factory(),
            'email' => $this->faker->safeEmail(),
            'contact_number' => $this->faker->phoneNumber(),
            'mode' => $this->faker->randomElement(['email', 'phone', 'chat']),
            'description' => $this->faker->paragraph(),
            'status' => 0,
        ];
    }
}
