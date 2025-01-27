<?php

namespace Cachet;

use Cachet\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Database\Eloquent\Model;
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
     * The user agent used by Cachet's webhooks.
     */
    public const WEBHOOK_USER_AGENT = 'Cachet/3.0 Webhook (+https://docs.cachethq.io)';

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
     * Get the configured user model.
     */
    public static function userModel(): Model
    {
        $userModel = config('cachet.user_model');

        return new $userModel;
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
        return '/'.ltrim(config('cachet.dashboard_path', app()->joinPaths(rtrim(static::path(), DIRECTORY_SEPARATOR), 'dashboard')), '/\\');
    }

    /**
     * Get the version of Cachet.
     */
    public static function version(): string
    {
        return trim(file_get_contents(__DIR__.'/../VERSION'));
    }

    /** @return array<string, list<string>> */
    public static function getResourceApiAbilities(): array
    {
        return [
            'components' => ['manage', 'delete'],
            'component-groups' => ['manage', 'delete'],
            'incidents' => ['manage', 'delete'],
            'incident-updates' => ['manage', 'delete'],
            'incident-templates' => ['manage', 'delete'],
            'metrics' => ['manage', 'delete'],
            'metric-points' => ['manage', 'delete'],
            'schedules' => ['manage', 'delete'],
            'schedule-updates' => ['manage', 'delete'],
        ];
    }

    /**
     * Determine if Cachet is running in demo mode.
     */
    public static function demoMode(): bool
    {
        return (bool) config('cachet.demo_mode');
    }
}
