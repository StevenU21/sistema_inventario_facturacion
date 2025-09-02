<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryMovement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'type',
        'adjustment_reason',
        'quantity',
        'unit_price',
        'total_price',
        'reference',
        'notes',
        'user_id',
        'inventory_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'adjustment_reason', 'quantity', 'unit_price', 'total_price', 'reference', 'notes', 'user_id', 'inventory_id']);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }

    public function getMovementTypeAttribute(): string
    {
        return match ($this->attributes['type'] ?? null) {
            'in' => 'Entrada',
            'out' => 'Salida',
            'adjustment' => 'Ajuste',
            'transfer' => 'Transferencia',
            'return' => 'Devolución',
            default => ucfirst($this->attributes['type'] ?? ''),
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
