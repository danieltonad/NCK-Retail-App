<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'inventory',
        'quantity'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

}