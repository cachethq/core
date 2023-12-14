<?php

namespace Cachet\Tests\Architecture;

use Cachet\Data\Data;

test('data objects test')
    ->expect('Cachet\Data')
    ->toBeClasses()
    ->toBeFinal()
    ->ignoring(Data::class)
    ->toExtend(Data::class);
