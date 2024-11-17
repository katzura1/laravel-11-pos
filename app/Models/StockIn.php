<?php

namespace App\Models;

use App\Models\StockInDetail;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'stock_in_no',
        'stock_in_date',
        'due_date',
        'supplier_id',
        'outlet_id',
        'user_id',
    ];

    public function detail()
    {
        return $this->hasMany(StockInDetail::class, 'stock_in_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
