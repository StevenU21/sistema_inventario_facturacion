<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'unit_price',
        'sub_total',
        'discount',
        'discount_amount',
        'product_variant_id',
        'sale_id',
    ];
}
