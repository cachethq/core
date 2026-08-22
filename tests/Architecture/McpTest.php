<?php

use Laravel\Mcp\Server\Tool;

test('mcp tools test')
    ->expect('Cachet\Mcp\Tools')
    ->toBeClasses()
    ->toExtend(Tool::class);
