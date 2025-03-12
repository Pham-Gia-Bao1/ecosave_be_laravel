<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaveProduct;
use App\Helpers\ApiResponse;
use Exception;

class SaveProductController extends Controller
{
    public function getSaveProductsByUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $today = now()->toDateString(); // Lấy ngày hiện tại

            // Lấy các sản phẩm của user, kiểm tra điều kiện expiry_date dựa trên reminder_days trong bảng
            $products = SaveProduct::where('user_id', $request->user_id)
                ->whereRaw("DATE(expiry_date) <= DATE_ADD(?, INTERVAL reminder_days DAY)", [$today])
                ->get(['id', 'code', 'expiry_date', 'reminder_days']);

            return response()->json([
                'success' => true,
                'products' => $products,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Đã xảy ra lỗi: " . $e->getMessage(),
            ], 500);
        }
    }
    public function getAllSaveProductsByUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            // Lấy tất cả sản phẩm theo user_id mà không kiểm tra ngày hiện tại
            $products = SaveProduct::where('user_id', $request->user_id)
                ->get(['id', 'code', 'expiry_date', 'reminder_days']);

            return ApiResponse::success($products, "Lấy danh sách lưu thành công");
        } catch (Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi lấy sản sản phẩm", ['error' => $e->getMessage()], 500);
        }
    }

    // Lưu sản phẩm vào bảng save_products
    public function storeSaveProduct(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'code' => 'required|string|unique:save_products,code',
                'expiry_date' => 'nullable|date',
                'reminder_days' => 'nullable|integer|min:0',
            ]);

            $saveProduct = SaveProduct::create([
                'user_id' => $request->user_id,
                'code' => $request->code,
                'expiry_date' => $request->expiry_date,
                'reminder_days' => $request->reminder_days ?? 0, // Mặc định là 0 nếu không có
            ]);

            return ApiResponse::success($saveProduct, "Sản phẩm đã được thêm thành công");
        } catch (Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi thêm sản phẩm", ['error' => $e->getMessage()], 500);
        }
    }

    // Kiểm tra sản phẩm có tồn tại hay không
    public function checkProductExists(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'code' => 'required|string',
            ]);

            $exists = SaveProduct::where('user_id', $request->user_id)
                ->where('code', $request->code)
                ->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Đã xảy ra lỗi: " . $e->getMessage(),
            ], 500);
        }
    }

    // Cập nhật thông tin sản phẩm đã lưu
    public function updateSaveProduct(Request $request, $id)
    {
        try {
            $request->validate([
                'code' => 'sometimes|string|unique:save_products,code,' . $id,
                'expiry_date' => 'nullable|date',
                'reminder_days' => 'nullable|integer|min:0',
            ]);

            $saveProduct = SaveProduct::findOrFail($id);
            $saveProduct->update($request->only(['code', 'expiry_date', 'reminder_days']));

            return ApiResponse::success($saveProduct, "Sản phẩm đã được cập nhật thành công");
        } catch (Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi cập nhật sản phẩm", ['error' => $e->getMessage()], 500);
        }
    }

    // Xóa sản phẩm đã lưu
    public function deleteSaveProduct($code)
    {
        try {
            $userId = auth()->id(); // Lấy ID của user đang đăng nhập
            $saveProduct = SaveProduct::where('code', $code)->where('user_id', $userId)->first();

            if (!$saveProduct) {
                return ApiResponse::error("Không tìm thấy sản phẩm hoặc bạn không có quyền xóa", [], 403);
            }

            $saveProduct->delete();

            return ApiResponse::success(null, "Sản phẩm đã được xóa thành công");
        } catch (Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi xóa sản phẩm", ['error' => $e->getMessage()], 500);
        }
    }
}
