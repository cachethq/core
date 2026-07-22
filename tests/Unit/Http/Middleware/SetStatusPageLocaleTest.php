<?php

use Cachet\Http\Middleware\SetStatusPageLocale;
use Cachet\Settings\AppSettings;
use Illuminate\Http\Request;

beforeEach(function () {
    config(['cachet.supported_locales' => [
        'en' => 'English',
        'fr' => 'Français',
        'de' => 'Deutsch',
    ]]);

    app()->setLocale('en');
});

it('applies the app settings locale when it is supported', function () {
    $settings = app(AppSettings::class);
    $settings->locale = 'fr';
    $settings->save();

    app(SetStatusPageLocale::class)->handle(Request::create('/'), fn () => null);

    expect(app()->getLocale())->toBe('fr');
});

it('leaves the app locale untouched when the setting is not a supported locale', function () {
    $settings = app(AppSettings::class);
    $settings->locale = 'xx';
    $settings->save();

    app(SetStatusPageLocale::class)->handle(Request::create('/'), fn () => null);

    expect(app()->getLocale())->toBe('en');
});

it('passes the request through to the next middleware', function () {
    $request = Request::create('/');
    $called = false;

    app(SetStatusPageLocale::class)->handle($request, function ($passed) use (&$called, $request) {
        expect($passed)->toBe($request);
        $called = true;

        return 'ok';
    });

    expect($called)->toBeTrue();
});
