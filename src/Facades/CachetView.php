<?php

namespace Cachet\Facades;

use Cachet\View\ViewManager;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Htmlable renderHook(string $name, string | array | null $scopes = null)
 *
 * @see ViewManager
 */
class CachetView extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ViewManager::class;
    }

    public static function registerRenderHook(string $name, Closure $hook, string|array|null $scopes = null): void
    {
        static::resolved(function (ViewManager $viewManager) use ($name, $hook, $scopes) {
            $viewManager->registerRenderHook($name, $hook, $scopes);
        });
    }
}
