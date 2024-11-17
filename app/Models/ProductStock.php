<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use Compoships;

    protected $fillable = [
        'outlet_id',
        'product_id',
        'stock',
    ];

    public function histories()
    {
        return $this->hasMany(ProductStockHistory::class, ['product_id','outlet_id'], ['product_id','outlet_id']);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
