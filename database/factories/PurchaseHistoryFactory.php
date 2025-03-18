<?php

namespace Database\Factories;

use App\Models\PurchaseHistory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseHistoryFactory extends Factory
{
    protected $model = PurchaseHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'order_id' => Order::inRandomOrder()->first()->id ?? Order::factory(),
            'purchase_date' => now(),
        ];
    }
}
