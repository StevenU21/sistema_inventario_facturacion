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

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('img/image03.png');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description']);
    }
}
