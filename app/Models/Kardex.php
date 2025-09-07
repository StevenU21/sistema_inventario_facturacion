<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    // No hay tabla física
    protected $table = null;
    public $timestamps = false;
    protected $guarded = [];

    public $product;
    public $warehouse;
    public $date_from;
    public $date_to;
    public $initial;
    public $rows;
    public $final;
}
