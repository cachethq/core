<?php

test('jobs test')
    ->expect('Cachet\Jobs')
    ->toBeClasses()
    ->toExtendNothing();
