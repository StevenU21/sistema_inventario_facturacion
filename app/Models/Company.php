<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'name',
        'ruc',
        'logo',
        'description',
        'address',
        'phone',
        'email'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'ruc', 'logo', 'description', 'address', 'phone', 'email']);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('img/image03.png');
    }
}
