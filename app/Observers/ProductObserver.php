<?php

namespace App\Observers;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
class ProductObserver
{
     public function created(Product $product): void
    {
        $this->clearCache();
    }

    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    public function deleted(Product $product): void
    {
        // Also clear transactions when product is deleted
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'products', 'categories', 'sellers', 'transactions'])->flush();
        } else {
            Cache::flush();
        }
    }

    private function clearCache(): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'products', 'categories', 'sellers'])->flush();
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
