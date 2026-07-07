<?php

namespace Cachet\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

class TagsFilter implements Filter
{
    /**
     * Filter the query to resources tagged with any of the given tags.
     *
     * @param  Builder<Model>  $query
     */
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $tags = array_filter(array_map('trim', (array) $value), fn (string $tag): bool => $tag !== '');

        if ($tags === []) {
            return;
        }

        $query->withAnyTags($tags);
    }
}
