<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr; // ✅ Thêm use Arr để sử dụng hàm random()

class ImageFactory extends Factory
{
    protected $model = Image::class;

    private $factoriesImage = [
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/2/2/2240000000008-1.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/2/0/2069530000001-bb.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/2/2/2275330000008.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/2/2/2275350000002-1.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/8/9/8936204030449.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/8/9/8936204030463.jpg.webp",
        "https://www.lottemart.vn/media/catalog/product/cache/300x300/8/9/8936204030579.jpg.webp"
    ];

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'image_url' => Arr::random($this->factoriesImage),
            'image_order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
