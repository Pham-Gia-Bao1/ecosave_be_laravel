<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Trả về response thành công.
     */
    public static function success($data = [], $message = "Thành công", $status = 200, $pagination = null)
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $status);
    }

    /**
     * Trả về response lỗi chung.
     */
    public static function error($message = "Lỗi xảy ra", $errors = [], $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Trả về response có phân trang.
     */
    public static function paginate(LengthAwarePaginator $query, $message = "Lấy danh sách thành công", $status = 200)
    {
        $data = $query->items();
        $pagination = [
            'current_page' => $query->currentPage(),
            'per_page' => $query->perPage(),
            'total' => $query->total(),
            'last_page' => $query->lastPage(),
            'next_page_url' => $query->nextPageUrl(),
            'prev_page_url' => $query->previousPageUrl(),
        ];

        return self::success($data, $message, $status, $pagination);
    }
}
