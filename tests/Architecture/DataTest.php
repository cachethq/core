<?php

use Cachet\Data\BaseData;
use Cachet\Data\BaseUpdateData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Spatie\LaravelData\Data;

test('data test')
    ->expect('Cachet\Data')
    ->toBeClasses()
    ->toExtend(BaseData::class)
    ->ignoring(FlexibleDateTimeCast::class)
    ->toBeFinal()
    ->ignoring([BaseData::class, BaseUpdateData::class]);

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
        'Cachet\Mcp',
    ]);

test('base data test')
    ->expect(BaseData::class)
    ->toHaveMethodsDocumented()
    ->toBeAbstract()
    ->toExtend(Data::class);

test('base update data test')
    ->expect(BaseUpdateData::class)
    ->toHaveMethodsDocumented()
    ->toBeAbstract()
    ->toExtend(BaseData::class);
