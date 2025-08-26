<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'identity_card',
        'ruc',
        'email',
        'phone',
        'address',
        'description',
        'is_client',
        'is_supplier',
        'is_active'
    ];
}
