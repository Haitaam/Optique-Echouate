<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'status' => Order::STATUS_PENDING,
            'total_price' => fake()->randomFloat(2, 100, 5000),
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'type' => 'normal',
        ];
    }
}
