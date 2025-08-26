<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitMeasure extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'description'
    ];  
}
