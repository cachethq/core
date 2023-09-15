<?php

use Illuminate\Foundation\Http\FormRequest;

test('requests test')
    ->expect('Cachet\Http\Requests')
    ->toBeClasses()
    ->toExtend(FormRequest::class)
    ->toOnlyBeUsedIn('Cachet\Http\Controllers');
