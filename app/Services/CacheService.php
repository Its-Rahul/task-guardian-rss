<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const TTL = 600; // 10 minutes

    public function remember(string $key, callable $callback)
    {
        return Cache::remember($key, self::TTL, $callback);
    }
} 