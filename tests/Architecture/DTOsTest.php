<?php

namespace Cachet\Tests\Architecture;

use Cachet\Data\Data;

test('DTOs should be final')
    ->expect('Cachet\DTOs')
    ->toBeClasses()
    ->toBeFinal()
    ->ignoring(Data::class)
    ->toExtend(Data::class);
