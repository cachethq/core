<?php

use Cachet\Data\BaseData;
use Spatie\LaravelData\Data;

test('data test')
    ->expect('Cachet\Data')
    ->toBeClasses()
    ->toExtend(BaseData::class)
    ->toBeFinal()
    ->ignoring(BaseData::class);

test('data requests test')
    ->expect('Cachet\Data\Requests')
    ->toHaveConstructor()
    ->toExtend(BaseData::class)
    ->toHaveSuffix('RequestData')
    ->toOnlyBeUsedIn([
        'Cachet\Actions',
        'Cachet\Data',
        'Cachet\Http\Controllers',
        'Cachet\Filament\Resources',
    ]);

test('base data test')
    ->expect(BaseData::class)
    ->toHaveMethodsDocumented()
    ->toBeAbstract()
    ->toExtend(Data::class);
