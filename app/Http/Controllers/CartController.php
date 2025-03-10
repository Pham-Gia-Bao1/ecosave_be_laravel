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
            return response()->json(['message' => 'Hiện tại không có sản phẩm nào trong giỏ hàng'], 200);
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
                'address' => $user->address
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
        // Kiểm tra xác thực người dùng
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Người dùng chưa đăng nhập.',
            ], 401);
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user_id = Auth::id();
        $product = Product::find($request->product_id);

        // Kiểm tra sản phẩm tồn tại
        if (!$product) {
            return response()->json([
                'error' => 'Sản phẩm không tồn tại.',
            ], 404);
        }

        // Lấy hoặc tạo giỏ hàng
        $cart = Cart::firstOrCreate(['user_id' => $user_id]);

        // Kiểm tra số lượng sản phẩm trong giỏ hàng hiện tại
        $existingCartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        // Tính toán tổng số lượng sản phẩm sẽ có trong giỏ hàng
        $totalRequestedQuantity = $request->quantity;
        if ($existingCartItem) {
            $totalRequestedQuantity += $existingCartItem->quantity;
        }

        // Kiểm tra số lượng tồn kho
        if ($totalRequestedQuantity > $product->stock_quantity) {
            // Tính toán số lượng tối đa có thể thêm
            $maxAddableQuantity = $product->stock_quantity - 
                ($existingCartItem ? $existingCartItem->quantity : 0);

            return response()->json([
                'error' => 'Hiện tại bạn chỉ có thêm tối đa '. ($product->stock_quantity) . ' sản phẩm này vào giỏ hàng.',
                'maxAddableQuantity' => $maxAddableQuantity,
                'currentStock' => $product->stock_quantity
            ], 400);
        }

        // Cập nhật hoặc tạo mới sản phẩm trong giỏ hàng
        if ($existingCartItem) {
            $existingCartItem->quantity += $request->quantity;
            $existingCartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        // Cập nhật tổng số sản phẩm trong giỏ hàng
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'message' => 'Thêm sản phẩm vào giỏ hàng thành công.',
            'total_items' => $totalItems,
        ], 200);
    }

    public function getCartDetail(Request $request, $storeId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return response()->json(['message' => 'Your cart is empty.'], 200);
        }

        // Lấy sản phẩm thuộc store_id cụ thể
        $cartItems = $cart->cartItems()
            ->whereHas('product.store', function ($query) use ($storeId) {
                $query->where('id', $storeId);
            })
            ->with('product.store', 'product.images')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'No items found for this store.'], 200);
        }

        // Lấy thông tin store từ sản phẩm đầu tiên
        $store = $cartItems->first()->product->store;

        $formattedCart = [
            'cart_id' => $cart->id,
            'user' => [
                'id' => $user->id,
                'name' => $user->username,
                'email' => $user->email,
                'address' => $user->address
            ],
            'store' => [
                'store_id' => $store->id,
                'store_name' => $store->store_name,
                'store_address' => $store->address,
                'store_latitude' => $store->latitude,
                'store_longitude' => $store->longitude,
                'items' => $cartItems->map(function ($cartItem) {
                    return [
                        'product_id' => $cartItem->product->id,
                        'name' => $cartItem->product->name,
                        'quantity' => $cartItem->quantity,
                        'stock_quantity'=> $cartItem->product->stock_quantity,
                        'original_price' => $cartItem->product->original_price,
                        'discounted_price' => $cartItem->product->discounted_price,
                        'subtotal' => $cartItem->quantity * $cartItem->product->discounted_price,
                        'images' => $cartItem->product->images
                    ];
                }),
            ]
        ];

        return ApiResponse::success($formattedCart, "Cart for store returned successfully");
    }

    public function removeItem(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found.'], 404);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found in cart.'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed successfully.']);
    }

    public function updateItemQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found.'], 404);
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found in cart.'], 404);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Item quantity updated successfully.', 'cart_item' => $cartItem]);
    }

}
