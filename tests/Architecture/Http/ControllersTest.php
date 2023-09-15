<?php

test('controllers test')
    ->expect('Cachet\Http\Controllers')
    ->toBeClasses()
    ->toHaveSuffix('Controller');
