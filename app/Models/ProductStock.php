<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'outlet_id',
        'product_id',
        'stock'
    ];
}
