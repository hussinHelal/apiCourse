<?php

namespace App\Observers;

use App\Models\Categories;
use Illuminate\Support\Facades\Cache;
class CategoriesObserver
{
     public function created(Categories $category): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'categories'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function updated(Categories $category): void
    {
        // Category updates affect products that display category info
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'categories', 'products'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function deleted(Categories $category): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'categories', 'products'])->flush();
        } else {
            Cache::flush();
        }
    }

    private function cacheSupportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'dynamodb', 'array']);
    }
}
