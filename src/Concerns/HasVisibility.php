<?php

namespace Cachet\Concerns;

use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibility
{
    /**
     * Scope to visible incidents.
     */
    public function scopeVisible(Builder $query, bool $authenticated = false): Builder
    {
        return $query->whereIn('visible', match ($authenticated) {
            true => ResourceVisibilityEnum::visibleToUsers(),
            default => ResourceVisibilityEnum::visibleToGuests(),
        });
    }

    /**
     * Scope the resource to a given visibility setting.
     */
    public function scopeVisibility(Builder $query, ResourceVisibilityEnum $visibility): Builder
    {
        return $query->where('visible', $visibility);
    }

    /**
     * Scope the resource to those visible to guests.
     */
    public function scopeGuests(Builder $query): Builder
    {
        return $query->whereIn('visible', ResourceVisibilityEnum::visibleToGuests());
    }

    /**
     * Scope the resource to those visible to authenticated users.
     */
    public function scopeUsers(Builder $query): Builder
    {
        return $query->whereIn('visible', ResourceVisibilityEnum::visibleToUsers());
    }
}
