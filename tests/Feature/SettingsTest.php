<?php

use Cachet\Models\Setting;
use Cachet\Support\Settings\Setting as SettingFacade;

it('can get a setting', function () {
    Setting::factory()->create();

    $appName = SettingFacade::get('app_name');

    expect($appName)->toBe('Cachet');
});

it('can determine that a setting exists', function () {
    Setting::factory()->create();

    $appName = SettingFacade::has('app_name');

    expect($appName)->toBeTrue();
});

it('can forget a setting', function () {
    Setting::factory()->create();

    SettingFacade::forget('app_name');

    $this->assertDatabaseMissing('settings', [
        'name' => 'app_name',
    ]);
});

it('can set a setting', function () {
    Setting::factory()->create();

    $setting = SettingFacade::set('app_name', 'Cachet 3.x');

    expect($setting)->toBe('Cachet 3.x');
});
