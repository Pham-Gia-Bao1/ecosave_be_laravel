<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SaveProduct;
use App\Models\User;
use Carbon\Carbon;

class SaveProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // Lấy một user mẫu
        if ($user) {
            SaveProduct::create([
                'user_id' => $user->id,
                'code' => '67c017ef378fb7033dac6132',
                'expiry_date' => Carbon::now()->addDays(30), // Hết hạn sau 30 ngày
                'reminder_days' => 5, // Nhắc nhở trước 5 ngày
            ]);
        }
    }
}
