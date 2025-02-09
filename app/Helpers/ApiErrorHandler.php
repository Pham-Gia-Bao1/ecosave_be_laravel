<?php

namespace App\Helpers;

class ApiErrorHandler
{
    /**
     * Trả về response khi có lỗi validation (422)
     */
    public static function validationError($errors = [], $message = "Dữ liệu không hợp lệ")
    {
        return ApiResponse::error($message, $errors, 422);
    }

    /**
     * Trả về response khi không tìm thấy tài nguyên (404)
     */
    public static function notFound($message = "Không tìm thấy tài nguyên")
    {
        return ApiResponse::error($message, [], 404);
    }

    /**
     * Trả về response khi không có quyền truy cập (403)
     */
    public static function forbidden($message = "Bạn không có quyền truy cập")
    {
        return ApiResponse::error($message, [], 403);
    }

    /**
     * Trả về response khi cần xác thực (401)
     */
    public static function unauthorized($message = "Không có quyền truy cập, vui lòng đăng nhập")
    {
        return ApiResponse::error($message, [], 401);
    }

    /**
     * Trả về response khi có lỗi server (500)
     */
    public static function serverError($message = "Lỗi máy chủ nội bộ")
    {
        return ApiResponse::error($message, [], 500);
    }

    /**
     * Trả về response khi request không hợp lệ (400)
     */
    public static function badRequest($message = "Yêu cầu không hợp lệ", $errors = [])
    {
        return ApiResponse::error($message, $errors, 400);
    }
}
