<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = ['name', 'default_faktur_pajak'];

    public function getDefaultFakturPajakAttribute($value)
    {
        return $value ? 'true' : 'false';
    }
}
