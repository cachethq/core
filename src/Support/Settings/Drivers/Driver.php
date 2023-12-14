<?php

namespace Cachet\Support\Settings\Drivers;

interface Driver
{
    /**
     * Get a setting.
     */
    public function get(string $key, $default = null);

    /**
     * Set a setting.
     */
    public function set(string $key, $value = null): mixed;

    /**
     * Determine if a setting exists.
     */
    public function has(string $key): bool;

    /**
     * Forget a setting.
     */
    public function forget(string $key): void;
}
