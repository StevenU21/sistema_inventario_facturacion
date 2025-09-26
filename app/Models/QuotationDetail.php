<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class QuotationDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'quantity',
        'unit_price',
        'discount',
        'discount_amount',
        'sub_total',
        'product_variant_id',
        'warehouse_id',
        'quotation_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'quantity',
                'unit_price',
                'discount',
                'discount_amount',
                'sub_total',
                'product_variant_id',
                'warehouse_id',
                'quotation_id',
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

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
