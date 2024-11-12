<?php

namespace App\Models;

use App\Models\SubBrand;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name'];

    public function subBrand()
    {
        return $this->hasMany(SubBrand::class);
    }
}
