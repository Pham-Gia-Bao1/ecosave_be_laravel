<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaveProduct extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'expiry_date', 'reminder_days'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
