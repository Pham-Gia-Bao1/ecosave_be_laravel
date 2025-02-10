<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
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

    public function getProductsByStore($storeId)
    {
        try {
            $store = Store::findOrFail($storeId);
            $products = $store->products()->with(['store', 'category'])->paginate(10);
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể lấy danh sách sản phẩm', 'message' => $e->getMessage()], 500);
        }
    }

    public function postAddProduct(Request $request, $storeId)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'original_price' => 'required|numeric|min:0',
                'discount_percent' => 'required|integer|min:0|max:100',
                'product_type' => 'required|string|max:255',
                'discounted_price' => 'nullable|numeric|min:0',
                'expiration_date' => 'nullable|date',
                'stock_quantity' => 'required|integer|min:0',
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:categories,id',
            ]);

            $store = Store::findOrFail($storeId);
            $productData = $request->all();
            $productData['store_id'] = $store->id;

            $product = Product::create($productData);
            return response()->json(['message' => 'Sản phẩm đã được thêm!', 'product' => $product], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Cửa hàng không tồn tại!'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dữ liệu không hợp lệ!', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi thêm sản phẩm!', 'message' => $e->getMessage()], 500);
        }
    }

    public function getProductByStore($storeId, $productId)
    {
        try {
            $product = Product::where('store_id', $storeId)->findOrFail($productId);
            return response()->json($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể lấy sản phẩm', 'message' => $e->getMessage()], 500);
        }
    }

    public function putUpdateProduct(Request $request, $storeId, $productId)
    {
        try {
            $product = Product::where('store_id', $storeId)->findOrFail($productId);

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'original_price' => 'required|numeric|min:0',
                'discount_percent' => 'required|integer|min:0|max:100',
                'product_type' => 'required|string|max:255',
                'discounted_price' => 'nullable|numeric|min:0',
                'expiration_date' => 'nullable|date',
                'stock_quantity' => 'required|integer|min:0',
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:categories,id',
            ]);

            $product->update($request->all());
            return response()->json(['message' => 'Sản phẩm đã được cập nhật!', 'product' => $product]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dữ liệu không hợp lệ!', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi cập nhật sản phẩm!', 'message' => $e->getMessage()], 500);
        }
    }
}
