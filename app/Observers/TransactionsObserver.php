<?php

namespace App\Observers;

use App\Models\Transactions;
use Illuminate\Support\Facades\Cache;
class TransactionsObserver
{
     public function created(Transactions $transaction): void
    {
        $this->clearCache();
    }

    public function updated(Transactions $transaction): void
    {
        $this->clearCache();
    }

    public function deleted(Transactions $transaction): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        // Transactions affect products (stock), buyers (orders), and sellers (sales)
        if ($this->cacheSupportsTagging()) {
            Cache::tags(['api', 'transactions', 'products', 'buyers', 'sellers'])->flush();
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
