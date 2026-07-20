<?php

namespace Cachet\Data\Casts;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTimeInterface;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

/**
 * Casts any date value accepted by the "date" validation rule, such as
 * "2023-11-07 05:31:56" or ISO 8601 ("2023-11-07T05:31:56Z"), to Carbon.
 */
final class FlexibleDateTimeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): DateTimeInterface|Uncastable
    {
        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            return Uncastable::create();
        }

        try {
            return Carbon::parse($value);
        } catch (InvalidFormatException) {
            return Uncastable::create();
        }
    }
}
