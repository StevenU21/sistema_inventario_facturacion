<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountReceivable extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'amount_due',
        'amount_paid',
        'status',
        'entity_id',
        'sale_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'amount_due',
                'amount_paid',
                'status',
                'entity_id',
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


    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTranslatedStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('Pendiente'),
            'partially_paid' => __('Parcialmente pagado'),
            'paid' => __('Pagado'),
            default => $this->status,
        };
    }
}
