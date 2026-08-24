<?php

use Illuminate\Console\Command;

test('commands test')
    ->expect('Cachet\Commands')
    ->toBeClasses()
    ->toExtend(Command::class)
    ->toHaveSuffix('Command');
