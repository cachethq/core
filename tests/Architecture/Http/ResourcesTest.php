<?php

use TiMacDonald\JsonApi\JsonApiResource;

test('resources test')
    ->expect('Cachet\Http\Resources')
    ->toBeClasses()
    ->toExtend(JsonApiResource::class)
    ->toOnlyBeUsedIn([
        'Cachet\Http\Controllers',
        'Cachet\Http\Resources',
    ]);
