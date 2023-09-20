<?php

namespace Cachet\Support\Settings\Drivers;

class FileDriver implements Driver
{
    public function __construct(
        protected EloquentDriver $eloquent
    ) {
        //
    }

    public function get(string $key, $default = null)
    {
        //
    }

    public function set(string $key, $value = null): mixed
    {
        return $this->eloquent->set($key, $value);
    }

    public function has(string $key): bool
    {
        //
    }

    public function forget(string $key): void
    {
        //
    }
}
