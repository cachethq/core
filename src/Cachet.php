<?php

namespace Cachet;

use Cachet\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Cachet
{
    /**
     * The user agent used by Cachet.
     *
     * @var string
     */
    public const USER_AGENT = 'Cachet/3.0 (+https://docs.cachethq.io)';

    /**
     * Get the current user using `cachet.guard`.
     */
    public static function user(?Request $request = null)
    {
        $guard = config('cachet.guard');

        if (is_null($request)) {
            return call_user_func(app('auth')->userResolver(), $guard);
        }

        return $request->user($guard);
    }

    /**
     * Register the Cachet routes.
     */
    public static function routes(): PendingRouteRegistration
    {
        Route::aliasMiddleware('cachet.guest', RedirectIfAuthenticated::class);

        return new PendingRouteRegistration;
    }

    /**
     * Get the URI path prefix used by Cachet.
     */
    public static function path(): string
    {
        return config('cachet.path', '/status');
    }

    /**
     * Get the URI path prefix used by Cachet's dashboard.
     */
    public static function dashboardPath(): string
    {
        return '/'.ltrim(config('cachet.dashboard_path', app()->joinPaths(rtrim(static::path(), '/'), 'dashboard')), '/');
    }

    /**
     * Get the version of Cachet.
     */
    public static function version(): string
    {
        return trim(file_get_contents(__DIR__.'/../VERSION'));
    }
}
