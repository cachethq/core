<?php

test('actions test')
    ->expect('Cachet\Actions')
    ->toBeClasses()
    ->toExtendNothing()
    ->classes()
    ->toHaveMethod('handle');
