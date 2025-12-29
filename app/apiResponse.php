<?php

namespace App;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

trait apiResponse
{

    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }
    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
protected function showAll(Collection $collection, ?int $code = 200, string $dataClass, ?int $perPage = null, bool $useCache = true)
    {
        if($collection->isEmpty()){
            return $this->successResponse(['data'=>$collection], $code);
        }
        if($dataClass)
        {
            $collection = $this->filterData($collection, $dataClass);
        }
        
        $collection = $this->sortData($collection);
        $paginated = $this->paginate($collection, $perPage);
        
        if($useCache && $this->shouldCache())
        {
            $cache = $this->cacheResponse($paginated);
        } else {
            $cache = $paginated;
        }
            return $this->successResponse($cache, $code);
    }

    protected function showOne(Model $model, $code = 200)
    {
        return response()->json(['data' => $model], $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return response()->json(['message' => $message], $code);
    }

    protected function sortData(Collection $collection)
    {
        if(request()->has('sort_by'))
        {
            // $attribute = request()->sort_by;
            $attribute = request()->input('sort_by');
            $direction = request()->input('sort_direction', 'asc');
            // $collection = $collection->sortBy($attribute);
            $collection = $direction === 'desc' 
                ? $collection->sortByDesc($attribute)
                : $collection->sortBy($attribute);
        } 
        
        return $collection->values();
    }
    
    protected function filterData(Collection $collection, string $dataClass)
    {
        foreach(request()->query() as $key => $value)
        {
            if (in_array($key, ['page', 'per_page', 'sort_by', 'sort_direction'])) {
                continue;
            }

           if(property_exists($dataClass, $key) && filled($value))
           {
            $collection = $collection->where($key, $value);
           }
        }

        return $collection;
    }

    protected function paginate(Collection $collection, ?int $perPage = null)
    {

           $perPage = request()->input('per_page', $perPage ?? 15); 
           $perPage = min($perPage, 100);

           $currentPage = LengthAwarePaginator::resolveCurrentPage();
           $currentPageItems = $collection->slice(($currentPage -1) * $perPage, $perPage)->values();
           
           $paginated = new LengthAwarePaginator($currentPageItems, $collection->count(), $perPage, $currentPage , [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
           ]);
           
           return $paginated;
    }

     protected function shouldCache(): bool
    {
        if (!config('api.cache.enabled', true) || !request()->isMethod('GET')) {
            return false;
        }

        $user = request()->user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return false;
        }

        return true;
    }

    protected function cacheResponse(LengthAwarePaginator $paginator):LengthAwarePaginator
    {
        
        $cacheKey = $this->getCacheKey();
        $cacheTtl = config('api.cache.ttl',3);
        if($this->cacheSupportsTagging())
        {
            $cacheTags = $this->getCacheTags();
            return Cache::tags($cacheTags)->remember($cacheKey, $cacheTtl, fn() => $paginator);
        }
        
        return Cache::remember($cacheKey, $cacheTtl, fn() => $paginator);

    }
    
      protected function cacheSupportsTagging(): bool
    {
        $driver = config('cache.default');
        $supportedDrivers = ['redis', 'memcached', 'dynamodb', 'array'];
        
        return in_array($driver, $supportedDrivers);
    }

    protected function getCacheKey(): string    
    {
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
    // $fullUrl= "{$url}?{$queryString}";
       
      return 'api:'.md5($url . $queryString);
    }

    protected function getCacheTags(): array
    {
        return ['api'];
    }

    protected function clearCache(array $tags = ['api']): void
    {
        Cache::tags($tags)->flush();
    }

    protected function clearCurrentEndpointCache():void
    {
        $cacheKey = $this->getCacheKey();
        $cacheTags= $this->getCacheTags();
        Cache::tags($cacheTags)->forget($cacheKey);
    }
}