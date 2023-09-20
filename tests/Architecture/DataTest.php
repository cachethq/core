<?php

namespace Cachet\Tests\Architecture;

use Cachet\Data\Data;

test('Data objects should be final classes and extend the base Cachet\Data\Data class')
    ->expect('Cachet\DTOs')
    ->toBeClasses()
    ->toBeFinal()
    ->ignoring(Data::class)
    ->toExtend(Data::class);
