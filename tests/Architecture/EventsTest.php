<?php

test('events test')
    ->expect('Cachet\Events')
    ->toBeClasses()
    ->toExtendNothing();
