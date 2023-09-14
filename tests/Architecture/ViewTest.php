<?php

use Illuminate\Contracts\Validation\InvokableRule;

test('view test')
    ->expect('Cachet\View')
    ->toBeClasses()
    ->toExtend(\Illuminate\View\Component::class);
