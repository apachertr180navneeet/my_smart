<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'booking_id' => null,
            'datetime' => now(),
            'discount' => 0,
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'payment_type' => 'cash',
            'txn_id' => $this->faker->uuid(),
            'payment_status' => 'completed',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['payment_status' => 'pending']);
    }
}
