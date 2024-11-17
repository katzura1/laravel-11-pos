<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class ProductStockHistory extends Model
{
    use Compoships;

    protected $fillable = [
        'outlet_id',
        'product_id',
        'stock',
        'description'
    ];

    public function productStock()
    {
        return $this->hasOne(ProductStock::class, ['product_id','outlet_id'], ['product_id','outlet_id']);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
