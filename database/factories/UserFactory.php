<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'username' => $this->faker->unique()->userName(),
            'contact_number' => $this->faker->unique()->numerify('##########'),
            'user_type' => 'user',
            'status' => 1,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'user_type' => 'admin',
        ]);
    }

    public function provider(): static
    {
        return $this->state(fn () => [
            'user_type' => 'provider',
        ]);
    }

    public function handyman(): static
    {
        return $this->state(fn () => [
            'user_type' => 'handyman',
        ]);
    }

    public function customer(): static
    {
        return $this->state(fn () => [
            'user_type' => 'user',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 0,
        ]);
    }
}
