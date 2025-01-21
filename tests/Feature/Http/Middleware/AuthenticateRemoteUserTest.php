<?php

use Cachet\Http\Middleware\AuthenticateRemoteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Workbench\App\User;

it('authenticates remote user if REMOTE_USER header is present', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $request = Request::create('/test', 'GET', [], [], [], ['HTTP_REMOTE_USER' => 'test@example.com']);

    $next = function ($request) {
        return new Response('OK');
    };

    $middleware = new AuthenticateRemoteUser;

    $response = $middleware->handle($request, $next);

    expect(Auth::check())->toBeTrue()
        ->and(Auth::user()->email)->toBe('test@example.com')
        ->and($response->getContent())->toBe('OK');
});

it('does not authenticate remote user if REMOTE_USER header is not present', function () {
    $request = Request::create('/test');

    $next = function ($request) {
        return new Response('OK');
    };

    $middleware = new AuthenticateRemoteUser;

    $response = $middleware->handle($request, $next);

    expect(Auth::check())->toBeFalse()
        ->and($response->getContent())->toBe('OK');
});
