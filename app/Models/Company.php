<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
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
}
