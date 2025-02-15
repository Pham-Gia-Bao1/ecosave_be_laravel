<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stores';

    protected $fillable = [
        'store_name',
        'avatar',
        'logo',
        'store_type',
        'opening_hours',
        'status',
        'contact_email',
        'contact_phone',
        'latitude',
        'longitude',
        'soft_description',
        'description',
        'address',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
