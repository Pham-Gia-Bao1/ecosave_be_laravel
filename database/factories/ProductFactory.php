<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $originalPrice = $this->faker->randomFloat(2, 10000, 500000);
        $discountPercent = $this->faker->numberBetween(0, 50);
        $discountedPrice = $originalPrice * (100 - $discountPercent) / 100;

        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'discounted_price' => $discountedPrice,
            'product_type' => 'store_selling',
            'stock_quantity' => $this->faker->numberBetween(10, 100),
            'store_id' => Store::factory(),
            'category_id' => Category::factory(),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'rating' => $this->faker->randomFloat(1, 0, 5), // Thêm rating
        ];
    }
}
