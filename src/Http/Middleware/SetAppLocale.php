<?php

namespace Cachet\Http\Middleware;

use Cachet\Models\User;
use Closure;
use Illuminate\Http\Request;

class SetAppLocale
{
    public function handle(Request $request, Closure $next)
    {
        /** @var ?User */
        $user = $request->user();

        if ($user) {
            app()->setLocale($user->preferredLocale() ?? $request->getPreferredLanguage(
                array_keys(config('cachet.supported_locales'))
            ));
        }

        return $next($request);
    }
}
