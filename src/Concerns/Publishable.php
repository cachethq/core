<?php

namespace Cachet\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Adds a publish gate to a resource. A resource is considered published when it
 * has no `published_at` timestamp, or its `published_at` timestamp is in the past.
 * Unpublished resources are hidden from public read surfaces until that moment.
 *
 * @method static Builder<static>|static published()
 * @method static Builder<static>|static unpublished()
 */
trait Publishable
{
    /**
     * Scope to resources that are visible to the public, i.e. published now or
     * with no publish date at all.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query->whereNull($this->qualifyColumn('published_at'))
                ->orWhere($this->qualifyColumn('published_at'), '<=', Carbon::now());
        });
    }

    /**
     * Scope to resources that are scheduled to publish in the future.
     */
    public function scopeUnpublished(Builder $query): void
    {
        $query->whereNotNull($this->qualifyColumn('published_at'))
            ->where($this->qualifyColumn('published_at'), '>', Carbon::now());
    }

    /**
     * Determine whether the resource is currently published.
     */
    public function isPublished(): bool
    {
        return $this->published_at === null || $this->published_at->isPast();
    }
}
