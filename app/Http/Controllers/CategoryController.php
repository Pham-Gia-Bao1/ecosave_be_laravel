<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // Lấy danh sách tất cả các danh mục
    public function index()
    {
        $categories = Category::all();
        return ApiResponse::success($categories, "Lấy danh sách loại sản phẩm thành công!");
    }

    // Tạo danh mục mới
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
            ]);

            $category = Category::create($validatedData);
            return ApiResponse::success($category, "Tạo danh mục thành công!", 201);
        } catch (ValidationException $e) {
            return ApiResponse::error($e->errors(), "Dữ liệu không hợp lệ!", 422);
        }
    }

    // Lấy thông tin chi tiết của một danh mục
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error(null, "Không tìm thấy danh mục!", 404);
        }
        return ApiResponse::success($category, "Lấy thông tin danh mục thành công!");
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return ApiResponse::error(null, "Không tìm thấy danh mục!", 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string',
            ]);

            $category->update($validatedData);
            return ApiResponse::success($category, "Cập nhật danh mục thành công!");
        } catch (ValidationException $e) {
            return ApiResponse::error($e->errors(), "Dữ liệu không hợp lệ!", 422);
        }
    }

    // Xóa danh mục
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error(null, "Không tìm thấy danh mục!", 404);
        }

        $category->delete();
        return ApiResponse::success(null, "Xóa danh mục thành công!");
    }
}
