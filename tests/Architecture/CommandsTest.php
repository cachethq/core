<?php

use Illuminate\Console\Command;

test('commands test')
    ->expect('Cachet\Console\Commands')
    ->toBeClasses()
    ->toExtend(Command::class)
    ->toHaveMethod('handle')
    ->toHaveSuffix('Command');
