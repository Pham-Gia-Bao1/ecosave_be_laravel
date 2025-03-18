<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'store_id' => Store::inRandomOrder()->first()->id ?? Store::factory(),
            'total_price' => fake()->randomFloat(2, 10, 500),
            'status' => fake()->randomElement(['completed', 'pending']),
            'order_code' => 'ECOSAVE' . $this->faker->unique()->numerify('###########'),
        ];
    }
}

