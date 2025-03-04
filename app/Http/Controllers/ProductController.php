<?php

namespace App\Http\Controllers;

use App\Events\ProductCreated;
use App\Helpers\ApiResponse;
use App\Models\Product;
use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        $products = $query->paginate(100);

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

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function checkStoreAccess($storeId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->role === 3) {
            $store = Store::where('id', $storeId)
                ->where('user_id', $user->id)
                ->first();
                
            return $store ? true : false;
        }
        
        return false;
    }

    public function getStoreId()
    {
        $user = Auth::user();
        if (!$user) {
            return ApiResponse::error("Người dùng chưa đăng nhập", [], 401);
        }

        $store = Store::where('user_id', $user->id)->first();
        if (!$store) {
            return ApiResponse::error("Người dùng không có cửa hàng", [], 404);
        }

        return ApiResponse::success(['storeId' => $store->id], "Lấy storeId thành công");
    }

    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'original_price' => $product->original_price,
            'discount_percent' => $product->discount_percent,
            'discounted_price' => $product->discounted_price,
            'product_type' => $product->product_type,
            'expiration_date' => $product->expiration_date,
            'stock_quantity' => $product->stock_quantity,
            'rating' => $product->rating,
            'category' => $product->category,
            'store' => $product->store,
            'images' => $product->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'order' => $image->image_order
                ];
            }),
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'deleted_at' => $product->deleted_at
        ];
    }

    public function getProductsByStoreName($storeId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền truy cập", [], 403);
        }

        $products = Product::where('store_id', $storeId)
            ->with(['store', 'category', 'images'])
            ->paginate(10);

        return ApiResponse::success($products, "Lấy danh sách sản phẩm thành công");
    }

    public function postAddProduct(Request $request, $storeId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền thêm sản phẩm", [], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'original_price' => 'required|numeric|min:0',
            'discount_percent' => 'required|integer|min:0|max:100',
            'product_type' => 'required|string|max:255',
            'discounted_price' => 'nullable|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*.image_url' => 'required|string',
            'images.*.image_order' => 'required|integer|min:0'
        ]);

        $productData = $request->all();
        $productData['store_id'] = $storeId;

        $product = Product::create($productData);

        if ($request->has('images')) {
            foreach ($request->images as $image) {
                $product->images()->create($image);
            }
        }

        $product->load(['store', 'category', 'images']);

        event(new ProductCreated($product));
        return response()->json([
            'message' => 'Sản phẩm đã được thêm!',
            'product' => $this->formatProduct($product)
        ], 201);
    }

    public function getProductByStore($storeId, $productId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền truy cập sản phẩm này", [], 403);
        }

        $product = Product::where('store_id', $storeId)
            ->with(['store', 'category', 'images'])
            ->find($productId);

        if (!$product) {
            return ApiResponse::error("Sản phẩm không tồn tại hoặc không thuộc về cửa hàng này", [], 404);
        }

        return ApiResponse::success($product, "Lấy thông tin sản phẩm thành công");
    }

    public function putUpdateProduct(Request $request, $storeId, $productId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền cập nhật sản phẩm này", [], 403);
        }

        $product = Product::where('store_id', $storeId)
            ->find($productId);

        if (!$product) {
            return ApiResponse::error("Sản phẩm không tồn tại hoặc không thuộc về cửa hàng này", [], 404);
        }

        // Validate và cập nhật sản phẩm
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'original_price' => 'required|numeric|min:0',
            'discount_percent' => 'required|integer|min:0|max:100',
            'product_type' => 'required|string|max:255',
            'discounted_price' => 'nullable|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'nullable|array',
            'images.*.image_url' => 'required|string',
            'images.*.image_order' => 'required|integer|min:0'
        ]);

        $product->update($request->except('images'));

        if ($request->has('images')) {
            $product->images()->delete();
            foreach ($request->images as $image) {
                $product->images()->create($image);
            }
        }

        return ApiResponse::success($product, "Sản phẩm đã được cập nhật thành công");
    }

    public function deleteProduct($storeId, $productId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền xóa sản phẩm này", [], 403);
        }

        $product = Product::where('store_id', $storeId)
            ->find($productId);

        if (!$product) {
            return ApiResponse::error("Sản phẩm không tồn tại hoặc không thuộc về cửa hàng này", [], 404);
        }

        $product->delete();

        return ApiResponse::success(null, "Sản phẩm đã được xóa thành công");
    }

    public function getTrashedProductsByStore($storeId)
    {
        try {
            if (!$this->checkStoreAccess($storeId)) {
                return ApiResponse::error("Bạn không có quyền truy cập cửa hàng này", [], 403);
            }

            $products = Product::onlyTrashed()
                ->where('store_id', $storeId)
                ->with(['store', 'category', 'images'])
                ->paginate(10);

            return ApiResponse::success($products, "Lấy danh sách sản phẩm đã xóa thành công");
        } catch (\Exception $e) {
            return ApiResponse::error("Lỗi khi lấy danh sách sản phẩm đã xóa", ['error' => $e->getMessage()], 500);
        }
    }

    public function restoreProduct($storeId, $productId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền khôi phục sản phẩm này", [], 403);
        }

        $product = Product::onlyTrashed()
            ->where('store_id', $storeId)
            ->find($productId);

        if (!$product) {
            return ApiResponse::error("Sản phẩm không tồn tại hoặc không thuộc về cửa hàng này", [], 404);
        }

        $product->restore();

        return ApiResponse::success($product, "Sản phẩm đã được khôi phục thành công");
    }

    public function forceDeleteProduct($storeId, $productId)
    {
        if (!$this->checkStoreAccess($storeId)) {
            return ApiResponse::error("Bạn không có quyền xóa vĩnh viễn sản phẩm này", [], 403);
        }

        $product = Product::onlyTrashed()
            ->where('store_id', $storeId)
            ->find($productId);

        if (!$product) {
            return ApiResponse::error("Sản phẩm không tồn tại hoặc không thuộc về cửa hàng này", [], 404);
        }

        $product->images()->delete();
        $product->forceDelete();

        return ApiResponse::success(null, "Sản phẩm đã được xóa vĩnh viễn thành công");
    }
}
