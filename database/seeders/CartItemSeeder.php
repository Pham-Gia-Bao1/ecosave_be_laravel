<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;

class CartItemSeeder extends Seeder
{
    public function run()
    {
        $carts = Cart::all();
        $products = Product::all();

        foreach ($carts as $cart) {
            $randomProducts = $products->random(rand(3, 5)); 

            foreach ($randomProducts as $product) {
                CartItem::create([
                    'cart_id'    => $cart->id, 
                    'product_id' => $product->id, 
                    'quantity'   => rand(1, 5), 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
 