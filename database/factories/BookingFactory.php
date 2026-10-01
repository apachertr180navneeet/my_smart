<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'service_id' => null,
            'provider_id' => null,
            'type' => 'service',
            'date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'start_at' => $this->faker->time('H:i:s'),
            'end_at' => $this->faker->time('H:i:s'),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'discount' => 0,
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
            'quantity' => 1,
            'description' => $this->faker->sentence(),
            'status' => 1,
            'address' => $this->faker->address(),
            'tax' => 0,
            'advance_paid_amount' => 0,
            'final_total_service_price' => 0,
            'final_total_tax' => 0,
            'final_sub_total' => 0,
            'final_discount_amount' => 0,
            'final_coupon_discount_amount' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 1]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 5]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 6]);
    }
}
