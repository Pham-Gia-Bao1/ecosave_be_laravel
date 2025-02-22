<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ApiErrorHandler;
use App\Models\Order;
use Illuminate\Http\Request;
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
}
