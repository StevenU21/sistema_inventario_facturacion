<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sale extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'total',
        'is_credit',
        'tax_percentage',
        'tax_amount',
        'sale_date',
        'user_id',
        'entity_id',
        'payment_method_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'total',
                'is_credit',
                'tax_percentage',
                'tax_amount',
                'sale_date',
                'user_id',
                'entity_id',
                'payment_method_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function accountReceivable()
    {
        return $this->hasOne(AccountReceivable::class);
    }

    // Total de descuento aplicado en la venta (suma de discount_amount en detalles)
    public function getDiscountTotalAttribute(): float
    {
        if ($this->relationLoaded('saleDetails')) {
            return (float) $this->saleDetails->sum('discount_amount');
        }
        // fallback si no está cargado el relation
        return (float) $this->saleDetails()->sum('discount_amount');
    }
}
