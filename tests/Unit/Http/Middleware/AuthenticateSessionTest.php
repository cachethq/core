<?php

use Cachet\Http\Middleware\AuthenticateSession;
use Illuminate\Http\Request;

it('redirects stale sessions to the cachet dashboard login', function () {
    $middleware = app(AuthenticateSession::class);

    $redirectTo = (new ReflectionMethod($middleware, 'redirectTo'))
        ->invoke($middleware, Request::create('/dashboard'));

    expect($redirectTo)->toBe(route('filament.cachet.auth.login'));
});
