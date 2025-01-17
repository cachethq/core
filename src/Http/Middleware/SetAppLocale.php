<?php

namespace Cachet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAppLocale
{
    public function handle(Request $request, Closure $next)
    {
        /** @var ?\Cachet\Models\User */
        $user = $request->user();

        if ($user) {
            app()->setLocale($user->preferredLocale() ?? $request->getPreferredLanguage(
                array_keys(config('cachet.supported_locales'))
            ));
        }

        return $next($request);
    }
}
