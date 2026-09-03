<?php

namespace Cachet\Concerns;

use Cachet\Models\Tag;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
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
            ->filter(fn (string $name): bool => Str::slug($name) !== '')
            ->unique(fn (string $name): string => Str::slug($name))
            ->keyBy(fn (string $name): string => Str::slug($name));

        $timestamp = now();

        if ($names->isNotEmpty()) {
            Tag::query()->insertOrIgnore($names->map(fn (string $name, string $slug): array => [
                'name' => $name,
                'slug' => $slug,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->values()->all());
        }

        $tagIds = Tag::query()
            ->whereIn('slug', $names->keys())
            ->pluck('id')
            ->map(fn (mixed $tagId): int => (int) $tagId);

        $this->syncTagIds($tagIds, $timestamp);
    }

    /**
     * @param  Collection<int, int>  $tagIds
     */
    private function syncTagIds(Collection $tagIds, CarbonInterface $timestamp): void
    {
        $connection = $this->getConnection();

        $connection->transaction(function () use ($connection, $tagIds, $timestamp): void {
            $connection->table('taggables')
                ->where('taggable_type', $this->getMorphClass())
                ->where('taggable_id', $this->getKey())
                ->delete();

            if ($tagIds->isEmpty()) {
                return;
            }

            $connection->table('taggables')->insertOrIgnore(
                $tagIds->map(fn (int $tagId): array => [
                    'tag_id' => $tagId,
                    'taggable_type' => $this->getMorphClass(),
                    'taggable_id' => $this->getKey(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ])->all(),
            );
        });

        $this->unsetRelation('tags');
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
