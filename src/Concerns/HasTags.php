<?php

namespace Cachet\Concerns;

use Cachet\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

trait HasTags
{
    /**
     * @return MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @param  list<string>  $names
     */
    public function syncTags(array $names): void
    {
        $names = collect($names)
            ->filter(fn (string $name): bool => trim($name) !== '')
            ->map(fn (string $name): string => trim($name))
            ->unique(fn (string $name): string => mb_strtolower($name))
            ->values();

        $this->tags()->sync($names->map(fn (string $name): int => Tag::query()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        )->id));
    }

    /**
     * @param  list<string>  $names
     * @param  Builder<static>  $query
     */
    public function scopeWithAnyTags(Builder $query, array $names): void
    {
        $slugs = collect($names)->map(fn (string $name): string => Str::slug(trim($name)))->filter();

        if ($slugs->isNotEmpty()) {
            $query->whereHas('tags', fn (Builder $tags) => $tags->whereIn('slug', $slugs));
        }
    }
}
