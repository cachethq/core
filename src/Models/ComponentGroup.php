<?php

namespace Cachet\Models;

use Cachet\Concerns\HasVisibility;
use Cachet\Database\Factories\ComponentGroupFactory;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComponentGroup extends Model
{
    use HasFactory, HasVisibility;

    protected $casts = [
        'order' => 'int',
        'collapsed' => ComponentGroupVisibilityEnum::class,
        'visible' => ResourceVisibilityEnum::class,
    ];

    protected $fillable = [
        'name',
        'order',
        'collapsed',
        'visible',
    ];

    /**
     * Get the components in the group.
     */
    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }

    public function isCollapsible(): bool
    {
        return match ($this->collapsed) {
            ComponentGroupVisibilityEnum::collapsed,
            ComponentGroupVisibilityEnum::collapsed_unless_incident => true,
            default => false,
        };
    }

    public function isExpanded(): bool
    {
        return match ($this->collapsed) {
            ComponentGroupVisibilityEnum::collapsed => false,
            ComponentGroupVisibilityEnum::collapsed_unless_incident => $this->hasActiveIncident(),
            ComponentGroupVisibilityEnum::expanded => true,
        };
    }

    public function hasActiveIncident(): bool
    {
        return Incident::query()
            ->whereIn('component_id', $this->components->pluck('id'))
            ->exists();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ComponentGroupFactory::new();
    }
}
