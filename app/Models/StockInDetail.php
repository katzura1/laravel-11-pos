<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInDetail extends Model
{
    protected $fillable = [
        'stock_in_id',
        'product_id',
        'qty',
        'price',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
