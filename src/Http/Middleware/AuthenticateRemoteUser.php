<?php

namespace Cachet\Http\Middleware;

use Cachet\Cachet;
use Cachet\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateRemoteUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($remoteUser = $request->headers->get('REMOTE_USER')) {
            $userModel = Cachet::userModel();
            /** @var User|null $user */
            $user = $userModel::query()->where('email', $remoteUser)->first();

            if ($user !== null) {
                auth()->login($user);
            }
        }

        return $next($request);
    }
}
