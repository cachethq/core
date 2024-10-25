<?php

namespace Cachet;

use Cachet\Http\Middleware\RedirectIfAuthenticated;
use Cachet\Settings\ThemeSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Cachet
{
    /**
     * The user agent used by Cachet.
     *
     * @var string
     */
    const USER_AGENT = 'Cachet/1.0';

    /**
     * Get the default CSS variables.
     *
     * @return array<string, <string,string>>
     */
    public static function cssVariables(): array
    {
        return [
            'background-light' => app(ThemeSettings::class)->light_background,
            'text-light' => app(ThemeSettings::class)->light_text,
            'zinc-50' => app(ThemeSettings::class)->zinc_50,
            'zinc-100' => app(ThemeSettings::class)->zinc_100,
            'zinc-200' => app(ThemeSettings::class)->zinc_200,
            'zinc-300' => app(ThemeSettings::class)->zinc_300,
            'zinc-400' => app(ThemeSettings::class)->zinc_400,
            'zinc-500' => app(ThemeSettings::class)->zinc_500,
            'zinc-600' => app(ThemeSettings::class)->zinc_600,
            'zinc-700' => app(ThemeSettings::class)->zinc_700,
            'zinc-800' => app(ThemeSettings::class)->zinc_800,
            'zinc-900' => app(ThemeSettings::class)->zinc_900,
            'primary-50' => app(ThemeSettings::class)->primary_50,
            'primary-100' => app(ThemeSettings::class)->primary_100,
            'primary-200' => app(ThemeSettings::class)->primary_200,
            'primary-300' => app(ThemeSettings::class)->primary_300,
            'primary-400' => app(ThemeSettings::class)->primary_400,
            'primary-500' => app(ThemeSettings::class)->primary_500,
            'primary-600' => app(ThemeSettings::class)->primary_600,
            'primary-700' => app(ThemeSettings::class)->primary_700,
            'primary-800' => app(ThemeSettings::class)->primary_800,
            'primary-900' => app(ThemeSettings::class)->primary_900,
            'white' => app(ThemeSettings::class)->white,
            'background-dark' => app(ThemeSettings::class)->dark_background,
            'text-dark' => app(ThemeSettings::class)->dark_text,
            'font-family-sans' => app(ThemeSettings::class)->font_family_sans,


        ];
    }

    /**
     * Get the current theme mode.
     *
     * @return string ('system', 'dark', 'light')
     */
    public static function darkMode(): string
    {
        return app(ThemeSettings::class)->dark_mode;
    }

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
