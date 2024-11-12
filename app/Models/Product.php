<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'sub_brand_id',
        'supplier_id',
        'product_category_id',
        'class',
        'buying_price',
        'selling_price',
    ] ;
}
