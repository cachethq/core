<?php

use Livewire\Component;

test('livewire component test')
    ->expect('Cachet\Livewire\Components')
    ->toBeClasses()
    ->toExtend(Component::class);
