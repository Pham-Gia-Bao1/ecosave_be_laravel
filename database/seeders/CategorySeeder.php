<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Thịt',
            'Thủy sản',
            'Trứng',
            'Trái Cây',
            'Thực Phẩm Đông Lạnh',
            'Thực Phẩm Sơ Chế',
            'Dầu Ăn, Gia vị',
            'Gạo, Mì, Bún, Đậu',
            'Thực Phẩm khô',
            'Chế Phẩm Từ Sữa',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'description' => null, 
            ]);
        }
    }
}
