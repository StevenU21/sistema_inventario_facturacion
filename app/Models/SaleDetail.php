<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SaleDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'quantity',
        'unit_price',
        'sub_total',
        'discount',
        'discount_amount',
        'product_variant_id',
        'sale_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'quantity',
                'unit_price',
                'sub_total',
                'discount',
                'discount_amount',
                'product_variant_id',
                'sale_id',
            ]);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
