<?php

use Cachet\Http\Middleware\SetAppLocale;
use Illuminate\Http\Request;
use Workbench\App\User;

beforeEach(function () {
    config(['cachet.supported_locales' => [
        'en' => 'English',
        'fr' => 'Français',
        'de' => 'Deutsch',
    ]]);

    app()->setLocale('en');
});

it('leaves the app locale untouched when no user is authenticated', function () {
    $request = Request::create('/');

    (new SetAppLocale)->handle($request, fn () => null);

    expect(app()->getLocale())->toBe('en');
});

it('uses the authenticated user\'s preferred locale when available', function () {
    $user = User::factory()->create(['preferred_locale' => 'fr']);
    $request = Request::create('/');
    $request->setUserResolver(fn () => $user);

    (new SetAppLocale)->handle($request, fn () => null);

    expect(app()->getLocale())->toBe('fr');
});

it('falls back to the request preferred language when the user has no preferred locale', function () {
    $user = User::factory()->create(['preferred_locale' => null]);
    $request = Request::create('/', 'GET', server: ['HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.9,en;q=0.8']);
    $request->setUserResolver(fn () => $user);

    (new SetAppLocale)->handle($request, fn () => null);

    expect(app()->getLocale())->toBe('de');
});

it('passes the request through to the next middleware', function () {
    $request = Request::create('/');
    $called = false;

    (new SetAppLocale)->handle($request, function ($passed) use (&$called, $request) {
        expect($passed)->toBe($request);
        $called = true;

        return 'ok';
    });

    expect($called)->toBeTrue();
});
