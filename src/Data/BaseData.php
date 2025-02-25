<?php

namespace Cachet\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @template TKey of array-key
 * @template TValue
 */
#[MapName(SnakeCaseMapper::class)]
abstract class BaseData extends Data
{
    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return array_filter(
            parent::toArray(),
            fn (mixed $value) => $value !== null,
        );
    }
}
