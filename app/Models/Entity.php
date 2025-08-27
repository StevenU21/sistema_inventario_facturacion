<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Entity extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'first_name',
        'last_name',
        'identity_card',
        'ruc',
        'email',
        'phone',
        'address',
        'description',
        'is_client',
        'is_supplier',
        'is_active'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'identity_card', 'ruc', 'email', 'phone', 'address', 'description', 'is_client', 'is_supplier', 'is_active']);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }
}
