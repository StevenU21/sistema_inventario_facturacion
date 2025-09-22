<?php

namespace App\Models;

use App\Traits\CacheClearable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'name',
        'description',
        'category_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'category_id']);
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
