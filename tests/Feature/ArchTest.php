<?php

use Illuminate\Database\Eloquent\Model;

test('the models namespace only contains classes')
    ->expect('Cachet\Models')
    ->toBeClasses()
    ->toExtend(Model::class);

test('the enums namespace only contains enums')
    ->expect('Cachet\Enums')
    ->toBeEnums();

test('globals')
    ->expect(['dd', 'dump'])
    ->not->toBeUsed();
