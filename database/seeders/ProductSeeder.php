<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $store = Store::create([
            'store_name' => 'Lotte Mart',
            'avatar' => 'https://www.lottemart.vn/data/images/logo.png',
            'store_type' => 'supermarket',
            'status' => 'active',
            'latitude' => 10.7769,
            'longitude' => 106.7009,
            'user_id' => 1,
        ]);

        $products = [
            [
                'name' => 'Sữa tươi tiệt trùng Vinamilk 100% Có đường 1L',
                'category' => 'Sữa và các sản phẩm từ sữa',
                'price' => 32000,
                'discount' => 0,
                'images' => [
                    'https://www.lottemart.vn/data/images/product/sua-tuoi-tiet-trung-vinamilk-100-co-duong-1l.jpg',
                    'https://www.lottemart.vn/data/images/product/sua-tuoi-tiet-trung-vinamilk-100-co-duong-1l-2.jpg'
                ],
                'expiration_date' => now()->addMonths(6),
            ],
            // Thêm các sản phẩm khác tương tự
        ];

        foreach ($products as $productData) {
            $category = Category::firstOrCreate(['name' => $productData['category']]);
            
            $discountedPrice = $productData['price'] * (100 - ($productData['discount'] ?? 0)) / 100;
            
            $product = Product::create([
                'name' => $productData['name'],
                'description' => 'Mô tả cho ' . $productData['name'],
                'original_price' => $productData['price'],
                'discounted_price' => $discountedPrice,
                'discount_percent' => $productData['discount'] ?? 0,
                'product_type' => 'store_selling',
                'stock_quantity' => rand(10, 100),
                'store_id' => $store->id,
                'category_id' => $category->id,
                'expiration_date' => $productData['expiration_date'] ?? null,
            ]);

            // Thêm ảnh cho sản phẩm
            foreach ($productData['images'] as $index => $imageUrl) {
                Image::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'image_order' => $index
                ]);
            }
        }
    }
}
