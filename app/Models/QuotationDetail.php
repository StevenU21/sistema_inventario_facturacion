<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'unit_price',
        'discount',
        'discount_amount',
        'sub_total',
        'product_variant_id',
        'quotation_id',
    ];
}
