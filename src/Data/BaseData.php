<?php

namespace Cachet\Data;

use Spatie\LaravelData\Data;

/**
 * @template TKey of array-key
 * @template TValue
 */
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
