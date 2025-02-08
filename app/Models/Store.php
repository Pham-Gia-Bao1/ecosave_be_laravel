<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    // Danh sách các cột có thể được gán hàng loạt
    protected $fillable = [
        'store_name',
        'avatar',
        'store_type',
        'opening_hours',
        'status',
        'contact_email',
        'contact_phone',
        'latitude',
        'longitude',
        'description',
        'user_id',
        'created_at',
        'updated_at',
    ];

    // Mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
