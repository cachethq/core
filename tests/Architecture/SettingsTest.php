<?php

use Spatie\LaravelSettings\Settings;

test('settings test')
    ->expect('Cachet\Settings')
    ->toBeClasses()
    ->toExtend(Settings::class)
    ->toHaveSuffix('Settings');
