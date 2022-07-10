<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'quantity',
        'price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
