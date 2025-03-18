<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

 

    protected $fillable = [
        'name',
        'description',
        'original_price',
        'discounted_price',
        'discount_percent',
        'expiration_date',
        'product_type',
        'stock_quantity',
        'store_id',
        'category_id',
        'rating',
        'origin', //nguồn gốc
        'ingredients',
        'usage_instructions', // Hướng dẫn sử dụng
        'storage_instructions' // Bảo quản
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
