<?php

use Cachet\Settings\AppSettings;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;
use function Pest\Laravel\getJson;


it('has api disabled', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = false;
    $settings->api_protected = false;
    $settings->save();

    getJson('/status/api/ping')
        ->assertNotFound();
});

it('has api disabled with protected and user', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = false;
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['general.ping']);

    getJson('/status/api/ping')
        ->assertNotFound();
});

it('has api disabled without protected and user', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = false;
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['general.ping']);

    getJson('/status/api/ping')
        ->assertNotFound();
});


it('has public api access', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = true;
    $settings->api_protected = false;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['general.ping']);

    getJson('/status/api/ping')
        ->assertOk();
});


it('has public api access with a user', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = true;
    $settings->api_protected = false;
    $settings->save();

    getJson('/status/api/ping')
        ->assertOk();
});

it('has no access to api without a user', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = true;
    $settings->api_protected = true;
    $settings->save();
    getJson('/status/api/ping')
        ->assertUnauthorized();
});


it('has access to api with a user', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = true;
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['general.ping']);

    getJson('/status/api/ping')
        ->assertOk();
});
