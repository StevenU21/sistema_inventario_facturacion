<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'reference',
        'subtotal',
        'total',
        'entity_id',
        'warehouse_id',
        'user_id',
        'payment_method_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'reference',
                'subtotal',
                'total',
                'entity_id',
                'warehouse_id',
                'user_id',
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

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}

