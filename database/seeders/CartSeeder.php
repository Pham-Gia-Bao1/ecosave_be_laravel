<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run()
    {
        $users = User::take(10)->get(); // Lấy 10 user đầu tiên

        foreach ($users as $user) {
            Cart::create([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}