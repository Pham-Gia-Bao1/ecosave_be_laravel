<?php

namespace App\Http\Controllers;
use App\Helpers\ApiResponse;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'items'      => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $orderItems[] = [
                'order_id'   => $validated['order_id'],
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        OrderItem::insert($orderItems);

        return ApiResponse::success($orderItems, "Lưu các sản phẩm được mua thành công!");

    }
}
