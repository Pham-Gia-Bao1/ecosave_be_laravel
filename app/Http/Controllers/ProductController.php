<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $latitude = $user->latitude;
        $longitude = $user->longitude;

        $products = Product::with(['store', 'category', 'images'])
        ->select('products.*')
        ->selectRaw("
            ( 6371 * acos( cos( radians(?) ) *
            cos( radians( stores.latitude ) )
            * cos( radians( stores.longitude ) - radians(?)
            ) + sin( radians(?) ) *
            sin( radians( stores.latitude ) ) )
            ) AS distance", [$latitude, $longitude, $latitude])
            ->join('stores', 'products.store_id', '=', 'stores.id')
            ->orderBy('distance')
            ->get();

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'images' => $product->images->map(function ($image) {
                    return [
                        'url' => $image->image_url,
                        'order' => $image->image_order
                    ];
                }),
                'distance' => round($product->distance, 2),
                'store_name' => explode(' ', $product->store->store_name)[0],
                'product_type' => $product->product_type,
                'original_price' => $product->original_price,
                'discount_price' => $product->discount_price,
                'discount_percent' => $product->discount_percent,
            ];
        });

        return response()->json($formattedProducts);
    }

    public function productDetail($id)
    {
        $product = Product::with(['store', 'category'])->findOrFail($id);

        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'image' => $product->image,
            'original_price' => $product->original_price,
            'discount_price' => $product->discount_price,
            'discount_percent' => $product->discount_percent,
            'expiration_date' => $product->expiration_date,
            'product_type' => $product->product_type,
            'stock_quantity' => $product->stock_quantity,
            'store' => [
                'id' => $product->store->id,
                'name' => $product->store->store_name,
                'avatar' => $product->store->avatar,
                'store_type' => $product->store->store_type,
                'opening_hours' => $product->store->opening_hours,
                'status' => $product->store->status,
                'contact_email' => $product->store->contact_email,
                'contact_phone' => $product->store->contact_phone,
                'latitude' => $product->store->latitude,
                'longitude' => $product->store->longitude,
                'description' => $product->store->description,
            ],
            'category' => [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'description' => $product->category->description,
            ],
        ];

        return response()->json($formattedProduct);
    }
}
