<?php

namespace Cachet\Models;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentGroup extends Model
{
    use HasFactory;

    protected $casts = [
        'order' => 'int',
        'collapsed' => ComponentGroupVisibilityEnum::class,
        'visible' => ResourceVisibilityEnum::class,
    ];

    protected $fillable = [
        'name',
        'order',
        'visible',
    ];

    /**
     * Get the components in the group.
     */
    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    /**
     * Scope component groups to a specific visibility.
     */
    public function scopeVisibility(Builder $query, ResourceVisibilityEnum $visibility): Builder
    {
        return $query->where('visible', $visibility);
    }

    public function scopeGuests(Builder $query): Builder
    {
        return $query->whereIn('visible', ResourceVisibilityEnum::visibleToGuests());
    }

    public function scopeUsers(Builder $query): Builder
    {
        return $query->whereIn('visible', ResourceVisibilityEnum::visibleToUsers());
    }
}
