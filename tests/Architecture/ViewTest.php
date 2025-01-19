<?php

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Component;

test('view component test')
    ->expect('Cachet\View\Components')
    ->toBeClasses()
    ->toExtend(Component::class);

test('view htmlable test')
    ->expect('Cachet\View\Htmlable')
    ->toBeClasses()
    ->toImplement(Htmlable::class);
