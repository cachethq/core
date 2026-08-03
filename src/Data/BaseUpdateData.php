<?php

namespace Cachet\Data;

use Spatie\LaravelData\Data;

/**
 * Base class for update request payloads.
 *
 * Unlike {@see BaseData}, an update payload must distinguish a field that was
 * not sent from a field explicitly sent as `null`: absent fields are typed
 * `Optional` and dropped from `toArray()`, while explicit nulls survive so
 * callers can clear nullable columns.
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @extends BaseData<TKey, TValue>
 */
abstract class BaseUpdateData extends BaseData
{
    /**
     * Get the instance as an array, keeping explicit nulls.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return Data::toArray();
    }
}
