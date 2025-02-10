<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Thực phẩm tươi sống',
            'Đồ uống',
            'Bánh kẹo',
            'Sữa và các sản phẩm từ sữa',
            'Đồ gia dụng',
            'Chăm sóc cá nhân',
            'Đồ dùng nhà bếp'
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
