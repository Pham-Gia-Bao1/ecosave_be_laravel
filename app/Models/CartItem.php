<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    // CartItem thuộc về một giỏ hàng
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // CartItem có một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
