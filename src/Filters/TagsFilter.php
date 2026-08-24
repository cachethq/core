<?php

namespace Cachet\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class TagsFilter implements Filter
{
    /**
     * @param  Builder<Model>  $query
     */
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $tags = collect((array) $value)
            ->flatMap(fn (string $tag): array => explode(',', $tag))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->values()
            ->all();

        if ($tags === []) {
            return;
        }

        $query->whereHas('tags', function (Builder $tagQuery) use ($tags): void {
            $tagQuery->whereIn('slug', collect($tags)->map(fn (string $tag): string => Str::slug($tag)));
        });
    }
}
