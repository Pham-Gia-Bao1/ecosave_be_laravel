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

        // Mảng điều kiện lọc
        $filters = [
            'store_id' => 'store_id',
            'expiration_date' => 'expiration_date',
            'min_price' => ['discounted_price', '>='],
            'max_price' => ['discounted_price', '<='],
        ];

        // Áp dụng bộ lọc động
        foreach ($filters as $param => $condition) {
            if ($request->has($param)) {
                if (is_array($condition)) {
                    $query->where($condition[0], $condition[1], $request->$param);
                } else {
                    $query->where($condition, $request->$param);
                }
            }
        }

        // Lọc theo ID cửa hàng (nếu có)
        $query->when($request->store_id, function ($q, $storeId) {
            return $q->where('store_id', $storeId);
        });

        // Lọc theo tên sản phẩm
        $query->when($request->name, function ($q, $name) {
            return $q->where('name', 'like', "%$name%");
        });

        // Lọc theo tên cửa hàng
        $query->when($request->store_name, function ($q, $storeName) {
            return $q->whereHas('store', fn($store) => $store->where('store_name', 'like', "%$storeName%"));
        });

        // Lọc theo nhiều danh mục
        $query->when($request->category_id, function ($q, $categoryIds) {
            return $q->whereIn('category_id', explode(',', $categoryIds));
        });

        // Lọc theo tên danh mục
        $query->when($request->category_name, function ($q, $categoryName) {
            return $q->whereHas('category', fn($category) => $category->where('name', 'like', "%$categoryName%"));
        });

        // Lọc theo đánh giá (rating) nếu hợp lệ
        $query->when($request->rating, function ($q, $rating) {
            return ($rating >= 0 && $rating <= 5)
                ? $q->where('rating', '>=', (float) $rating)
                : ApiResponse::error('Giá trị rating không hợp lệ. Vui lòng nhập số từ 0 đến 5.', 400);
        });

        // Phân trang
        $products = $query->paginate(10);

        return ApiResponse::paginate($products, "Lấy danh sách sản phẩm thành công");
    }



    public function productDetail($id)
    {
        try {
            $product = Product::with(['store', 'category', 'reviews.user', 'images'])->find($id); // ✅ Thêm 'reviews'
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
    // public function getAllProductsByStoreId($storeId)
    // {
    //     try {
    //         $store = Store::findOrFail($storeId);
    //         $products = $store->products()->paginate(10);
    //         return response()->json($products);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Không thể lấy danh sách sản phẩm', 'message' => $e->getMessage()], 500);
    //     }
    // }

}
