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
        'is_active',
        'municipality_id'
    ];

    /**
     * Get the formatted phone number with Nicaraguan country code.
     * Example: +505 85850002
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        // Remove non-digit characters
        $raw = preg_replace('/\D/', '', $this->phone);
        // Only keep last 8 digits for local number
        $local = substr($raw, -8);

        return '+505 ' . $local;
    }

    public function getFormattedIdentityCardAttribute(): ?string
    {
        if (!$this->identity_card) {
            return null;
        }
        // Remove non-alphanumeric characters
        $raw = preg_replace('/[^A-Za-z0-9]/', '', $this->identity_card);
        // Match the expected pattern: 3 digits, 6 digits, 4 digits, 1 letter (optional)
        if (preg_match('/^(\d{3})(\d{6})(\d{4})([A-Za-z]?)$/', $raw, $matches)) {
            $formatted = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
            if (!empty($matches[4])) {
                $formatted .= strtoupper($matches[4]);
            }
            return $formatted;
        }
        // If it doesn't match, return as is
        return $this->identity_card;
    }

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

    public function scopeVisibleFor($query, $user)
    {
        $canViewClients = $user && $user->can('read clients');
        $canViewSuppliers = $user && $user->can('read suppliers');

        $query->where('is_active', true);

        if ($canViewClients && !$canViewSuppliers) {
            $query->where('is_client', true)->where('is_supplier', false);
        } elseif (!$canViewClients && $canViewSuppliers) {
            $query->where('is_supplier', true)->where('is_client', false);
        } elseif ($canViewClients && $canViewSuppliers) {
            $query->where(function ($q) {
                $q->where('is_client', true)->orWhere('is_supplier', true);
            });
        } else {
            $query->whereRaw('0=1');
        }

        return $query;
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
