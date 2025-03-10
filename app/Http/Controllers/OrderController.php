<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Helpers\ApiErrorHandler;
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

    // Lấy danh sách đơn hàng theo cửa hàng
    public function getOrdersByStore(Request $request, $storeId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $status = $request->query('status', 'pending');
        $search = $request->query('search', '');
        $perPage = $request->query('perPage', 10);

        $query = Order::where('store_id', $this->storeId)
            ->with(['user', 'orderItems.product.images'])
            ->when($status, function ($query) use ($status) {
                if ($status === 'deleted') {
                    return $query->onlyTrashed();
                }
                return $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest();

        $orders = $query->paginate($perPage);

        return ApiResponse::success([
            'data' => $orders->map(fn($order) => $this->formatOrder($order)),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ], "Lấy danh sách đơn hàng thành công");
    }

    // Lấy danh sách đơn hàng đã xóa (soft delete)
    public function getDeletedOrders($storeId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $orders = Order::onlyTrashed()->where('store_id', $this->storeId)->get();

        return ApiResponse::success($orders->map(fn($order) => $this->formatOrder($order)), "Lấy danh sách đơn hàng đã xóa thành công");
    }

    // Lấy chi tiết đơn hàng
    public function getOrderDetail($storeId, $orderId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $order = Order::where('id', $orderId)->where('store_id', $this->storeId)->first();

        if (!$order) {
            return ApiResponse::error("Đơn hàng không tồn tại", [], 404);
        }

        return ApiResponse::success($this->formatOrder($order), "Lấy chi tiết đơn hàng thành công");
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus(Request $request, $storeId, $orderId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $order = Order::where('id', $orderId)->where('store_id', $this->storeId)->first();

        if (!$order) {
            return ApiResponse::error("Đơn hàng không tồn tại", [], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,completed'
        ]);

        $order->status = $request->status;
        $order->save();

        return ApiResponse::success($this->formatOrder($order), "Cập nhật trạng thái đơn hàng thành công");
    }

    // Xóa đơn hàng (soft delete)
    public function deleteOrder($storeId, $orderId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $order = Order::where('id', $orderId)->where('store_id', $this->storeId)->first();

        if (!$order) {
            return ApiResponse::error("Đơn hàng không tồn tại", [], 404);
        }

        $order->delete();

        return ApiResponse::success(null, "Xóa đơn hàng thành công");
    }

    // Xóa vĩnh viễn đơn hàng
    public function forceDeleteOrder($storeId, $orderId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $order = Order::onlyTrashed()->where('id', $orderId)->where('store_id', $this->storeId)->first();

        if (!$order) {
            return ApiResponse::error("Đơn hàng không tồn tại hoặc chưa bị xóa tạm thời", [], 404);
        }

        $order->forceDelete();

        return ApiResponse::success(null, "Xóa vĩnh viễn đơn hàng thành công");
    }

    // Khôi phục đơn hàng đã xóa
    public function restoreOrder($storeId, $orderId)
    {
        if (!$this->storeId || $this->storeId != $storeId) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $order = Order::onlyTrashed()->where('id', $orderId)->where('store_id', $this->storeId)->first();

        if (!$order) {
            return ApiResponse::error("Đơn hàng không tồn tại hoặc chưa bị xóa tạm thời", [], 404);
        }

        $order->restore();

        return ApiResponse::success($this->formatOrder($order), "Khôi phục đơn hàng thành công");
    }

    // Định dạng dữ liệu đơn hàng
    private function formatOrder($order)
    {
        $firstItem = $order->orderItems->isNotEmpty() ? $order->orderItems->first() : null;
        $firstProduct = $firstItem ? $firstItem->product : null;
        $firstImage = $firstProduct && $firstProduct->images->isNotEmpty()
            ? $firstProduct->images->first()->image_url
            : null;

        return [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'user' => [
                'id' => $order->user->id,
                'username' => $order->user->username,
                'email' => $order->user->email,
                'phone' => $order->user->phone_number,
            ],
            'total_price' => $order->total_price,
            'status' => $order->status,
            'order_date' => $order->created_at,
            'main_product' => [
                'name' => $firstProduct ? $firstProduct->name : 'N/A',
                'image' => $firstImage,
                'quantity' => $firstItem ? $firstItem->quantity : 0,
            ],
            'total_items' => $order->orderItems->count(),
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->price,
                        'image' => $item->product->images->isNotEmpty()
                            ? $item->product->images->first()->image_url
                            : null,
                    ],
                    'quantity' => $item->quantity,
                ];
            }),
        ];
    }
}
