<?php

namespace App\Models;

use App\Models\SubBrand;
use App\Models\Supplier;
use App\Models\ProductCategory;
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


    public function subBrand()
    {
        return $this->belongsTo(SubBrand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
