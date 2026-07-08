<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;

class EnsureApiIsEnabled
{
    public function __construct(private AppSettings $settings) {}

    public function handle(Request $request, Closure $next)
    {
        abort_unless($this->settings->api_enabled, 404);

        return $next($request);
    }
}
