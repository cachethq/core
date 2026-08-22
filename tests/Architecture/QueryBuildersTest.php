<?php

use Illuminate\Database\Eloquent\Builder;

test('query builders test')
    ->expect('Cachet\QueryBuilders')
    ->toBeClasses()
    ->toExtend(Builder::class)
    ->toHaveSuffix('Builder');
