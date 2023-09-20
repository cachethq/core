<?php

namespace Cachet\Support\Settings\Drivers;

use Cachet\Models\Setting;

class EloquentDriver implements Driver
{
    public function get(string $key, $default = null)
    {
        return Setting::query()
            ->where('name', $key)
            ->first()
            ->value ?? $default;
    }

    public function set(string $key, $value = null): mixed
    {
        Setting::query()
            ->updateOrCreate(
                ['name' => $key],
                ['value' => $value],
            );

        return $value;
    }

    public function has(string $key): bool
    {
        return Setting::query()
            ->where('name', $key)
            ->exists();
    }

    public function forget(string $key): void
    {
        Setting::query()
            ->where('name', $key)
            ->delete();
    }
}
