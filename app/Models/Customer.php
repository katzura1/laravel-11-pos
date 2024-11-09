<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'identity_number',
        'phone_number',
        'birth_date',
    ];

    public static function getLastCode()
    {
        //random 4 digit random character
        return Str::random(4);
    }
}
