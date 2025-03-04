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
            // Validate request
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'expiry_date' => 'nullable|date',
            ]);

            // Query save products
            $query = SaveProduct::where('user_id', $request->user_id);

            if ($request->filled('expiry_date')) {
                $query->whereDate('expiry_date', $request->expiry_date);
            }

            // Lấy danh sách các code
            $productCodes = $query->pluck('code')->toArray();

            // Trả về danh sách productIds
            return response()->json([
                'success' => true,
                'productIds' => $productCodes,
            ]);
        } catch (Exception $e) {
            // Xử lý lỗi hệ thống
            return response()->json([
                'success' => false,
                'message' => "Đã xảy ra lỗi: " . $e->getMessage(),
            ], 500);
        }
    }

    // API lưu sản phẩm vào bảng save_products
    public function storeSaveProduct(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'code' => 'required|string|unique:save_products,code',
                'expiry_date' => 'nullable|date',
            ]);

            // Lưu sản phẩm vào database
            $saveProduct = SaveProduct::create([
                'user_id' => $request->user_id,
                'code' => $request->code,
                'expiry_date' => $request->expiry_date,
            ]);

            return ApiResponse::success($saveProduct, "Sản phẩm đã được thêm thành công");
        } catch (Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi thêm sản phẩm", ['error' => $e->getMessage()], 500);
        }
    }
    public function checkProductExists(Request $request)
{
    try {
        // Validate request
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|string',
        ]);

        // Kiểm tra xem mã code có tồn tại không
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

}
