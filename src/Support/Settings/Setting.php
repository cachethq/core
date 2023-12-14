<?php

namespace Cachet\Support\Settings;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed set(string $key, mixed $value)
 * @method static void forget(string $key)
 * @method static bool has(string $key)
 */
class Setting extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingManager::class;
    }
}
