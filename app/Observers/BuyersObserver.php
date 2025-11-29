<?php

namespace App\Observers;

use App\Models\Buyer;
use Illuminate\Support\Facades\Cache;
class BuyersObserver
{
   public function created(Buyer $buyer): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'buyers', 'users'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function updated(Buyer $buyer): void
    {
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'buyers', 'users'])->flush();
        } else {
            Cache::flush();
        }
    }

    public function deleted(Buyer $buyer): void
    {
        // Also clear transactions when buyer is deleted
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'buyers', 'users', 'transactions'])->flush();
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
