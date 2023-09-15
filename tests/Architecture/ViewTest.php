<?php

use Illuminate\View\Component;

test('view test')
    ->expect('Cachet\View')
    ->toBeClasses()
    ->toExtend(Component::class);
