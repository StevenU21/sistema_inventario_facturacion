<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount_due',
        'amount_paid',
        'status',
        'entity_id',
        'sale_id',
    ];
}
