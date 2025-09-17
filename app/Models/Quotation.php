<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Quotation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'total',
        'valid_until',
        'status',
        'user_id',
        'entity_id',
    ];
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'valid_until' => 'datetime',
    ];
    /**
     * Date attributes for Carbon conversion.
     */
    protected $dates = [
        'valid_until',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'total',
                'valid_until',
                'status',
                'user_id',
                'entity_id',
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

    public function QuotationDetails()
    {
        return $this->hasMany(QuotationDetail::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until ? now()->startOfDay()->gt($this->valid_until) : false;
    }
}
