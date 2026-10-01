<?php

namespace Database\Factories;

use App\Models\PromotionalBanner;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionalBannerFactory extends Factory
{
    protected $model = PromotionalBanner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'banner_type' => $this->faker->randomElement(['image', 'video']),
            'banner_redirect_url' => $this->faker->url(),
            'is_requested_banner' => 0,
            'status' => 1,
            'duration' => $this->faker->randomElement([7, 14, 30]),
            'charges' => $this->faker->randomFloat(2, 10, 200),
            'start_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'end_date' => $this->faker->dateTimeBetween('+30 days', '+60 days'),
            'total_amount' => $this->faker->randomFloat(2, 10, 200),
            'payment_method' => 'stripe',
            'payment_status' => 'completed',
        ];
    }
}
