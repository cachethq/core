<?php

use Illuminate\Contracts\Validation\InvokableRule;

test('rules test')
    ->expect('Cachet\Rules')
    ->toBeClasses()
    ->toImplement(InvokableRule::class)
    ->toOnlyBeUsedIn('Cachet\Data');
