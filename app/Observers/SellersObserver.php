<?php

namespace App\Observers;

use App\Models\Seller;
use Illuminate\Support\Facades\Cache;
class SellersObserver
{
    public function created(Seller $seller): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'sellers', 'users'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function updated(Seller $seller): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'sellers', 'users'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function deleted(Seller $seller): void
    {
        // Also clear products and transactions when seller is deleted
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'sellers', 'users', 'products', 'transactions'])->flush();
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
