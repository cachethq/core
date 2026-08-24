<?php

use Spatie\QueryBuilder\Filters\Filter;

test('filters test')
    ->expect('Cachet\Filters')
    ->toBeClasses()
    ->toImplement(Filter::class)
    ->toHaveSuffix('Filter');
