<?php

namespace App\Traits;

use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Support\Facades\Log;

trait CacheClearable
{
    public static function bootCacheClearable()
    {
        static::created(fn($model) => $model->clearCache());
        static::updated(fn($model) => $model->clearCache());
        static::deleted(fn($model) => $model->clearCache());
    }

    public function clearCache()
    {
        ResponseCache::clear();
    }
}
