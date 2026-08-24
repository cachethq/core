<?php

use Cachet\Data\Casts\FlexibleDateTimeCast;
use Carbon\Carbon;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

it('casts supported date values to carbon', function (mixed $value, string $expected) {
    $result = (new FlexibleDateTimeCast)->cast(
        mock(DataProperty::class),
        $value,
        [],
        mock(CreationContext::class),
    );

    expect($result)
        ->toBeInstanceOf(Carbon::class)
        ->and($result->toIso8601String())->toBe($expected);
})->with([
    'date time' => [new DateTimeImmutable('2026-08-10T12:30:00+00:00'), '2026-08-10T12:30:00+00:00'],
    'ISO 8601 string' => ['2026-08-10T13:30:00+01:00', '2026-08-10T12:30:00+00:00'],
    'timestamp' => [1786365000, '2026-08-10T12:30:00+00:00'],
]);

it('returns uncastable for unsupported or invalid values', function (mixed $value) {
    $result = (new FlexibleDateTimeCast)->cast(
        mock(DataProperty::class),
        $value,
        [],
        mock(CreationContext::class),
    );

    expect($result)->toBeInstanceOf(Uncastable::class);
})->with([
    'null' => null,
    'boolean' => true,
    'array' => [[]],
    'object' => [new stdClass],
    'invalid string' => 'not a date',
]);
