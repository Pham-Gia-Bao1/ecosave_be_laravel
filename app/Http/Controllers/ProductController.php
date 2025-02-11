<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
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
        $query = Product::with(['store', 'category', 'images']);

        // Tìm kiếm theo tên sản phẩm
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Lọc theo tên cửa hàng
        if ($request->has('store_name')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('store_name', 'like', '%' . $request->store_name . '%');
            });
        }

            // Lọc theo nhiều danh mục (nhiều category_id)
        if ($request->has('category_id')) {
            $categoryIds = explode(',', $request->category_id); // Chuyển chuỗi thành mảng
            $query->whereIn('category_id', $categoryIds);
        }

        // Lọc theo tên danh mục (vẫn giữ cách cũ)
        if ($request->has('category_name')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category_name . '%');
            });
        }


        // Lọc theo ngày hết hạn
        if ($request->has('expiration_date')) {
            $query->whereDate('expiration_date', '=', $request->expiration_date);
        }

        // Lọc theo khoảng giá (min - max)
        if ($request->has('min_price')) {
            $query->where('discounted_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('discounted_price', '<=', $request->max_price);
        }

        // Lọc theo đánh giá (rating)
        if ($request->has('rating')) {
            $rating = (float) $request->get('rating');
            if ($rating >= 0 && $rating <= 5) {
                $query->where('rating', '>=', $rating);
            } else {
                return ApiResponse::error('Giá trị rating không hợp lệ. Vui lòng nhập số từ 0 đến 5.', 400);
            }
        }

        // Phân trang
        $products = $query->paginate(10);

        return ApiResponse::paginate($products, "Lấy danh sách sản phẩm thành công");
    }


    public function productDetail($id)
    {
        try {
            $product = Product::with(['store', 'category'])->find($id);
            if (!$product) {
                return ApiResponse::error("Sản phẩm không tồn tại", [], 404);
            }
            return ApiResponse::success($product, "Lấy thông tin sản phẩm thành công");
        } catch (\Exception $e) {
            return ApiResponse::error("Lỗi xảy ra khi lấy sản phẩm", ['error' => $e->getMessage()], 500);
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

    public function deleteProduct($storeId, $productId)
    {
        try {
            $product = Product::where('store_id', $storeId)->findOrFail($productId);
            $product->delete();
            return response()->json(['message' => 'Sản phẩm đã được xóa!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi xóa sản phẩm!', 'message' => $e->getMessage()], 500);
        }
    }

    public function getTrashedProductsByStore($storeId)
    {
        try {
            $products = Product::onlyTrashed()->where('store_id', $storeId)->paginate(10);
            if ($products->isEmpty()) {
                return response()->json(['message' => 'Không có sản phẩm nào bị xóa!'], 404);
            }
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi lấy danh sách sản phẩm đã xóa!', 'message' => $e->getMessage()], 500);
        }
    }

    public function restoreProduct($storeId, $productId)
    {
        try {
            $product = Product::onlyTrashed()->where('store_id', $storeId)->findOrFail($productId);
            $product->restore();
            return response()->json(['message' => 'Sản phẩm đã được khôi phục!', 'product' => $product]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sản phẩm không tồn tại hoặc chưa bị xóa!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi khôi phục sản phẩm!', 'message' => $e->getMessage()], 500);
        }
    }

    public function forceDeleteProduct($storeId, $productId)
    {
        try {
            $product = Product::onlyTrashed()->where('store_id', $storeId)->findOrFail($productId);
            $product->forceDelete();
            return response()->json(['message' => 'Sản phẩm đã được xóa vĩnh viễn!']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sản phẩm không tồn tại hoặc chưa bị xóa!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi khi xóa vĩnh viễn sản phẩm!', 'message' => $e->getMessage()], 500);
        }
    }
}
