<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;

class SetStatusPageLocale
{
    public function __construct(private AppSettings $settings) {}

    /**
     * Apply the instance-wide locale setting to the current request.
     *
     * Wrapped in rescue() so a missing settings table (pre-setup, pre-migration) is not fatal.
     */
    public function handle(Request $request, Closure $next)
    {
        rescue(function (): void {
            if (array_key_exists($this->settings->locale, config('cachet.supported_locales', []))) {
                app()->setLocale($this->settings->locale);
            }
        }, report: false);

        return $next($request);
    }
}
