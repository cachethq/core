<?php

use Cachet\Data\BaseData;
use Spatie\LaravelData\Data;

test('data test')
    ->expect('Cachet\Data')
    ->toBeClasses()
    ->toExtend(BaseData::class)
    ->toBeFinal()
    ->ignoring(BaseData::class);

test('base data test')
    ->expect(BaseData::class)
    ->toHaveMethodsDocumented()
    ->toBeAbstract()
    ->toExtend(Data::class);
