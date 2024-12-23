<?php

use Illuminate\View\Component;

test('view test')
    ->expect('Cachet\View\Components')
    ->toBeClasses()
    ->toExtend(Component::class);
