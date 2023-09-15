<?php

use Illuminate\Database\Eloquent\Model;

test('models test')
    ->expect('Cachet\Models')
    ->toBeClasses()
    ->toExtend(Model::class);
