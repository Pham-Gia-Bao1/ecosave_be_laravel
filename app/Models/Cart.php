<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    // Giỏ hàng thuộc về một user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Giỏ hàng có nhiều CartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
