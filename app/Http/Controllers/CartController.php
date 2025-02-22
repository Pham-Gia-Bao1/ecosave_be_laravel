<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;


class CartController extends Controller
{
    // API: Lấy giỏ hàng của người dùng
    public function getCart()
{
    if (!Auth::check()) {
        return response()->json(['error' => 'User not authenticated.'], 401);
    }

    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    // Lấy giỏ hàng của user
    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['message' => 'Your cart is empty.'], 200);
    }

    $cartItems = $cart->cartItems()->with('product.store', 'product.images')->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'No items in the cart.'], 200);
    }

    // Tổng số lượng sản phẩm & tổng số loại sản phẩm trong giỏ hàng
    $totalItems = $cartItems->sum('quantity');
    $totalProducts = $cartItems->unique('product_id')->count();

    // Nhóm các sản phẩm theo store
    $groupedItems = $cartItems->groupBy(function ($cartItem) {
        return $cartItem->product->store->id; // Nhóm theo ID của cửa hàng
    });

    // Format dữ liệu trả về
    $formattedCart = [
        'cart_id' => $cart->id,
        'user' => [
            'id' => $user->id,
            'name' => $user->username,
            'email' => $user->email,
        ],
        'total_items' => $totalItems, // Tổng số lượng sản phẩm trong giỏ hàng
        'total_products' => $totalProducts, // Tổng số loại sản phẩm trong giỏ hàng
        'stores' => $groupedItems->map(function ($items, $storeId) {
            $store = $items->first()->product->store; // Lấy thông tin store từ sản phẩm

            $totalQuantity = $items->sum('quantity'); // Tổng số lượng sản phẩm trong store
            $totalAmount = $items->sum(fn ($cartItem) => $cartItem->quantity * $cartItem->product->discounted_price); // Tổng tiền cho store
            $totalProductsPerStore = $items->unique('product_id')->count(); // Tổng số loại sản phẩm khác nhau trong store

            return [
                'store_id' => $store->id,
                'store_name' => $store->store_name,
                'store_address' => $store->address,
                'store_latitude' => $store->latitude,
                'store_longitude' => $store->longitude,
                'total_items_per_store' => $totalQuantity, // Tổng số lượng sản phẩm trong store
                'total_products_per_store' => $totalProductsPerStore, // Tổng số loại sản phẩm khác nhau trong store
                'total_amount' => $totalAmount,
                'items' => $items->map(function ($cartItem) {
                    $product = $cartItem->product;

                    return [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $cartItem->quantity,
                        'original_price' => $product->original_price,
                        'price' => $product->discounted_price,
                        'subtotal' => $cartItem->quantity * $product->discounted_price,
                        'images' => $product->images
                    ];
                }),
            ];
        }),
    ];
    return ApiResponse::success($formattedCart, "Cart returned successfully");
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user_id = Auth::id();
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        if ($product->stock_quantity < $request->quantity) {
            return response()->json(['error' => 'This product is out of stock.'], 400);
        }

        // Lấy hoặc tạo giỏ hàng
        $cart = Cart::firstOrCreate(['user_id' => $user_id]);

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            // Cập nhật số lượng sản phẩm trong giỏ hàng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Tạo mới sản phẩm trong giỏ hàng
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        // Cập nhật tổng số sản phẩm trong giỏ hàng
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'total_items' => $totalItems,
        ], 200);
    }
}
