<?php

use Illuminate\Database\Eloquent\Model;

test('models test')
    ->expect('Cachet\Models')
    ->toBeClasses()
    ->ignoring('Cachet\Models\Concerns')
    ->toExtend(Model::class)
    ->ignoring('Cachet\Models\Concerns');
