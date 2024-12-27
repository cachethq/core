<?php

namespace Cachet\Concerns;

use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder<static>|static query()
 * @method static Builder<static>|static visible(bool $authenticated = false)
 * @method static Builder<static>|static visibility(ResourceVisibilityEnum $visibility)
 * @method static Builder<static>|static users()
 * @method static Builder<static>|static guests()
 */
trait HasVisibility
{
    /**
     * Scope to visible incidents.
     */
    public function scopeVisible(Builder $query, bool $authenticated = false): void
    {
        $query->whereIn('visible', match ($authenticated) {
            true => ResourceVisibilityEnum::visibleToUsers(),
            default => ResourceVisibilityEnum::visibleToGuests(),
        });
    }

    /**
     * Scope the resource to a given visibility setting.
     */
    public function scopeVisibility(Builder $query, ResourceVisibilityEnum $visibility): void
    {
        $query->where('visible', $visibility);
    }

    /**
     * Scope the resource to those visible to guests.
     */
    public function scopeGuests(Builder $query): void
    {
        $query->whereIn('visible', ResourceVisibilityEnum::visibleToGuests());
    }

    /**
     * Scope the resource to those visible to authenticated users.
     */
    public function scopeUsers(Builder $query): void
    {
        $query->whereIn('visible', ResourceVisibilityEnum::visibleToUsers());
    }
}
