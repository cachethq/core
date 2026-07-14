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
                $query->where('key', $key)->whereIn('value', $this->encodings($expected));
            });
        }
    }

    /**
     * Normalise the incoming filter value into a list of key/value pairs.
     *
     * @return array<string, bool|string>
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

            $pairs[(string) $key] = is_bool($expected) ? $expected : (string) $expected;
        }

        return $pairs;
    }

    /**
     * Get the stored JSON encodings a filter value should match.
     *
     * Metadata values keep their JSON types in storage, while filter input
     * arrives as a string (or a boolean, once the query builder converts
     * `true`/`false`), so `?filter[meta][priority]=3` must match both the
     * integer 3 and the string "3".
     *
     * @return list<string>
     */
    protected function encodings(bool|string $value): array
    {
        if (is_bool($value)) {
            $candidates = [$value, $value ? 'true' : 'false'];
        } else {
            $candidates = [$value];

            if (is_numeric($value)) {
                $candidates[] = str_contains($value, '.') ? (float) $value : (int) $value;
            }

            if ($value === 'true' || $value === 'false') {
                $candidates[] = $value === 'true';
            }
        }

        return array_values(array_unique(array_map(
            fn (mixed $candidate): string => (string) json_encode($candidate),
            $candidates,
        )));
    }
}
