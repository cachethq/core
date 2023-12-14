<?php

namespace Cachet\Support\Settings\Drivers;

use Illuminate\Cache\Repository;

class CacheDriver implements Driver
{
    public function __construct(
        protected Repository $cache,
        protected EloquentDriver $eloquent
    ) {
        //
    }

    public function get(string $key, $default = null)
    {
        return $this->cache->rememberForever('setting:'.$key, function () use ($key, $default) {
            return $this->eloquent->get($key, $default);
        });
    }

    public function set(string $key, $value = null): mixed
    {
        return $this->eloquent->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->cache->has('setting:'.$key);
    }

    public function forget(string $key): void
    {
        $this->cache->forget('setting:'.$key);
    }
}
