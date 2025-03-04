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
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgfqHhJ7mrZPqj5-eI7KQkY_lfbGddYeE6eg&s',
                'address' => 'Mỹ Khê 3',
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
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOPlWYURGYJ0yymrDxeA3ezcrn6pceVA-_fg&s',
                'address' => '123 Main St',
                'role' => 3, // Thay 'role_id' thành 'role'
                'phone_number' => '0958494003',
                'latitude' => null, // Giá trị mặc định null
                'longitude' => null, // Giá trị mặc định null
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "quyen", 
                'email' => "quyen@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('quyen@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://png.pngtree.com/png-clipart/20190619/original/pngtree-hand-painted-cartoon-beauty-avatar-png-image_3978904.jpg',
                'address' => 'Mỹ Phong Phù Mỹ Bình Định',
                'role' => 2, 
                'phone_number' => '0958494223',
                'latitude' => '14.26286', 
                'longitude' => '109.07678', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Lotte Mart Đà Nẵng", 
                'email' => "Lottedn@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('wimartvn@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuwJveohkd40gaomjYSyhSFGiOQKUNZBzRaQ&s',
                'address' => '255 Hùng Vương, Vĩnh Trung, Thanh Khê',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0621', 
                'longitude' => '108.245', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "WinMart Nguyễn Tất Thành", 
                'email' => "wimartntt@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('wimartvn@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '180 Nguyễn Tất Thành, Hải Châu, Đà Nẵng 550000, Vietnam',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0589', 
                'longitude' => '108.2402', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Co.opMart Nguyễn Văn Linh", 
                'email' => "co.opmartnvl@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('Co.opMart@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQCFSpW2sn0Ihsl83K8AcGj3DrpBwyk3e0E1A&s',
                'address' => '02 Nguyễn Văn Linh, Hải Châu, Đà Nẵng 550000, Vietnam',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0634', 
                'longitude' => '108.2475', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Go Đà Nẵng", 
                'email' => "go@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('go@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTHAJoYKwa5HX6r7WmTlsdNBJAdbCo3ipP6Dw&s',
                'address' => '255-257 Hùng Vương, Vĩnh Trung, Đà Nẵng 550000, Vietnam',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0567', 
                'longitude' => '108.2389', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "WinMart Nguyễn Hữu Thọ", 
                'email' => "winmartnht@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('winmartnht@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '123 Nguyễn Hữu Thọ, Hòa Thuận Tây',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0598', 
                'longitude' => '108.2421', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Co.opMart Lê Đình Dương", 
                'email' => "coopmartldd@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('coopmartldd@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '89 Lê Đình Dương, Hải Châu, Đà Nẵng',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0615', 
                'longitude' => '108.2467', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Co.opMart Lê Duẩn", 
                'email' => "coopmartld@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('coopmartld@gmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '15 Lê Duẩn, Hải Châu',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0573', 
                'longitude' => '108.2445', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Co.opMart Nguyễn Chánh", 
                'email' => "coopmartncgmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('coopmartncgmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '150 Nguyễn Chánh, Sơn Trà',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0642', 
                'longitude' => '108.2411', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Otis Mart", 
                'email' => "otismartgmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('otismartgmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '100 Nguyễn Văn Thoại, Sơn Trà',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0556', 
                'longitude' => '108.2493', 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => "Co.opMart Lê Đại Hành", 
                'email' => "coopmartldhgmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('coopmartldhgmail.com'),
                'is_active' => true, 
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7iQt4EvOf05K8za3meiIM790VivfEmPgdbg&s',
                'address' => '170 Lê Đại Hành, Thanh Khê',
                'role' => 3, 
                'phone_number' => '0958494223',
                'latitude' => '16.0661', 
                'longitude' => '108.2439', 
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($data);
    }
}
