<?php

test('enums test')
    ->expect('Cachet\Enums')
    ->toBeEnums()
    ->toHaveSuffix('Enum');
