<?php

namespace Cachet\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MetaFilter implements Filter
{
    /**
     * Filter a resource by one or more metadata key/value pairs.
     *
     * Expects the filter value to be an associative array, e.g.
     * `?filter[meta][region]=eu-west` becomes `['region' => 'eu-west']`. Every
     * provided pair must match for a record to be included.
     *
     * @param  Builder<covariant \Illuminate\Database\Eloquent\Model>  $query
     */
    public function __invoke(Builder $query, $value, string $property): void
    {
        foreach ($this->pairs($value) as $key => $expected) {
            $query->whereHas('meta', function (Builder $query) use ($key, $expected) {
                $query->where('key', $key)->where('value', json_encode($expected));
            });
        }
    }

    /**
     * Normalise the incoming filter value into a list of key/value pairs.
     *
     * @return array<string, string>
     */
    protected function pairs(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $pairs = [];

        foreach ($value as $key => $expected) {
            if ($key === '' || is_array($expected)) {
                continue;
            }

            $pairs[(string) $key] = (string) $expected;
        }

        return $pairs;
    }
}
