<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        $data = [
            [
                'username' => 'admin', // Thay 'name' thành 'username'
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin@gmail.com'),
                'is_active' => true, // Sử dụng 'is_active'
                'avatar' => 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-photo-men-doctor-physician-chest-smiling-png-image_13143575.png',
                'address' => '123 Main St',
                'role' => 1, // Thay 'role_id' thành 'role'
                'phone_number' => '1234567890',
                'latitude' => null, // Giá trị mặc định null
                'longitude' => null, // Giá trị mặc định null
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "giabao", // Thay 'name' thành 'username'
                'email' => "giabao@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('giabao@gmail.com'),
                'is_active' => true, // Sử dụng 'is_active'
                'avatar' => 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-photo-men-doctor-physician-chest-smiling-png-image_13143575.png',
                'address' => '123 Main St',
                'role' => 3, // Thay 'role_id' thành 'role'
                'phone_number' => '0958494003',
                'latitude' => null, // Giá trị mặc định null
                'longitude' => null, // Giá trị mặc định null
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('user')->insert($data);

        // Thêm dữ liệu bằng factory
        User::factory()->count(5)->create();
    }
}
