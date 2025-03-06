<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Helpers\ApiErrorHandler;
use App\Models\Product;
use Exception;

class OrderController extends Controller
{
    /**
     * Lấy danh sách tất cả các đơn hàng (có phân trang).
     */
    public function index()
    {
        try {
            $orders = Order::with(['user', 'store'])->paginate(10);
            return ApiResponse::paginate($orders, "Danh sách đơn hàng được lấy thành công.");
        } catch (Exception $e) {
            return ApiErrorHandler::serverError("Lỗi khi lấy danh sách đơn hàng.");
        }
    }

    /**
     * Tạo đơn hàng mới.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'store_id' => 'required|exists:stores,id',
                'total_price' => 'required|numeric|min:0',
                'status' => 'required|string',
                'order_code' => 'required|unique:orders,order_code',
            ]);

            $order = Order::create($validatedData);

            return ApiResponse::success($order, "Tạo đơn hàng thành công.", 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiErrorHandler::validationError($e->errors());
        } catch (\Exception $e) {
            return ApiErrorHandler::serverError("Lỗi khi tạo đơn hàng: " . $e->getMessage());
        }
    }


    /**
     * Lấy thông tin chi tiết của một đơn hàng.
     */
    public function show($id)
    {
        try {
            $order = Order::with(['user', 'store'])->find($id);

            if (!$order) {
                return ApiErrorHandler::notFound("Đơn hàng không tồn tại.");
            }

            return ApiResponse::success($order, "Lấy thông tin đơn hàng thành công.");
        } catch (Exception $e) {
            return ApiErrorHandler::serverError("Lỗi khi lấy thông tin đơn hàng.");
        }
    }

    /**
     * Cập nhật thông tin đơn hàng.
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return ApiErrorHandler::notFound("Đơn hàng không tồn tại.");
            }

            $validatedData = $request->validate([
                'total_price' => 'numeric|min:0',
                'status' => 'string',
                'order_code' => "unique:orders,order_code,$id",
            ]);

            $order->update($validatedData);

            return ApiResponse::success($order, "Cập nhật đơn hàng thành công.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiErrorHandler::validationError($e->errors());
        } catch (Exception $e) {
            return ApiErrorHandler::serverError("Lỗi khi cập nhật đơn hàng.");
        }
    }

    /**
     * Xóa đơn hàng.
     */
    public function destroy($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return ApiErrorHandler::notFound("Đơn hàng không tồn tại.");
            }

            $order->delete();

            return ApiResponse::success([], "Xóa đơn hàng thành công.");
        } catch (Exception $e) {
            return ApiErrorHandler::serverError("Lỗi khi xóa đơn hàng.");
        }
    }

    private $storeId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if ($user && $user->role === 3) {
                $store = Store::where('user_id', $user->id)->first();

                $this->storeId = $store ? $store->id : null;
            } else {
                $this->storeId = null;
            }

            return $next($request);
        });
    }

    public function getOrdersByStore()
    {
        if (!$this->storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $orders = Order::where('store_id', $this->storeId)
            ->with(['user', 'orderItems.product'])
            ->paginate(10);

        return ApiResponse::success($orders, "Lấy danh sách đơn hàng thành công");
    }

    private function formatOrder($order)
    {
        return [
            'id' => $order->id,
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'total_price' => $order->total_price,
            'status' => $order->status,
            'order_date' => $order->order_date,
            'order_code' => $order->order_code,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->discounted_price ?? $item->product->original_price,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            }),
        ];
    }

    public function getUserOrders()
    {
        $user = Auth::user(); // Lấy user hiện tại
        if (!$user) {
            return ApiResponse::error(null, "Unauthorized", 401);
        }

        $orders = Order::where('user_id', $user->id)
            ->with(['store', 'orderItems.product'])
            ->get()
            ->groupBy('store_id');

        $formattedOrders = $orders->map(function ($ordersByStore) {
            $store = $ordersByStore->first()->store;
            return [
                'store_id' => $store->id,
                'store_name' => $store->store_name,
                'orders' => $ordersByStore->map(function ($order) {
                    return [
                        'order_id' => $order->id,
                        'order_code' => $order->order_code,
                        'total_price' => $order->total_price,
                        'status' => $order->status,
                        'items' => $order->orderItems->map(function ($item) {
                            return [
                                'product_id' => $item->product->id,
                                'product_name' => $item->product->name,
                                'product_image' => $item->product->images->pluck('image_url'),
                                'quantity' => $item->quantity,
                                'sub_price' => $item->price,
                                'unique_price' => $item->product->discounted_price
                            ];
                        }),
                        'order_date' => $order->order_date
                    ];
                }),
            ];
        });

        return ApiResponse::success($formattedOrders, "Orders fetched successfully");
    }

}
