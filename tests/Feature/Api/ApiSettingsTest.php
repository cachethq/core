<?php

use Cachet\Settings\AppSettings;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('allows guests to read the api by default', function () {
    getJson('/status/api/ping')
        ->assertOk();
});

it('responds not found when the api is disabled', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = false;
    $settings->save();

    getJson('/status/api/ping')
        ->assertNotFound();
});

it('responds not found when the api is disabled, even for authenticated users', function () {
    $settings = app(AppSettings::class);
    $settings->api_enabled = false;
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['*']);

    getJson('/status/api/ping')
        ->assertNotFound();
});

it('requires authentication for read endpoints when the api is protected', function () {
    $settings = app(AppSettings::class);
    $settings->api_protected = true;
    $settings->save();

    getJson('/status/api/ping')
        ->assertUnauthorized();
});

it('allows authenticated users to read the api when the api is protected', function () {
    $settings = app(AppSettings::class);
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['*']);

    getJson('/status/api/ping')
        ->assertOk();
});

it('still guards write endpoints by token abilities when the api is protected', function () {
    $settings = app(AppSettings::class);
    $settings->api_protected = true;
    $settings->save();

    Sanctum::actingAs(User::factory()->create(), ['components.view']);

    postJson('/status/api/components', [
        'name' => 'New Component',
    ])->assertForbidden();
});
