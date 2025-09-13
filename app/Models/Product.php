<?php

namespace App\Models;

use App\Traits\CacheClearable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\ProductVariant;

class Product extends Model
{
    use HasFactory, LogsActivity, CacheClearable;

    protected $fillable = [
        'name',
        'description',
        'code',
        'sku',
        'image',
        'status',
        'brand_id',
        'category_id',
        'tax_id',
        'unit_measure_id',
        'entity_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'description',
                'code',
                'status',
                'brand_id',
                'category_id',
                'tax_id',
                'unit_measure_id',
                'entity_id',
            ]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('img/image03.png');
    }

    public function getFormattedCreatedAtAttribute(): ?string
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i:s') : null;
    }

    public function getFormattedUpdatedAtAttribute(): ?string
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null;
    }

    // Relaciones Eloquent
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function unitMeasure()
    {
        return $this->belongsTo(UnitMeasure::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    // Variantes del producto (necesario para filtros por almacén en las búsquedas)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
