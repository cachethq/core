<?php

test('globals')
    ->expect(['dd', 'dump', 'ray', 'sleep', 'ddd', 'die', 'exit'])
    ->not->toBeUsed();
