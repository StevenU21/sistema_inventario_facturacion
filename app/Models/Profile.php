<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar',
        'phone',
        'identity_card',
        'gender',
        'address',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function getFormattedIdentityCardAttribute(): ?string
    {
        $ced = $this->identity_card;
        if ($ced && preg_match('/^([0-9]{3})([0-9]{6})([0-9]{5}[A-Za-z]?)$/', $ced, $m)) {
            return $m[1] . '-' . $m[2] . '-' . $m[3];
        }
        return $ced ?: null;
    }
}
