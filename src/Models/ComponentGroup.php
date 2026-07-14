<?php

namespace Cachet\Models;

use Cachet\Concerns\HasMeta;
use Cachet\Concerns\HasVisibility;
use Cachet\Concerns\Metable;
use Cachet\Database\Factories\ComponentGroupFactory;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $order
 * @property ComponentGroupVisibilityEnum $collapsed
 * @property ResourceVisibilityEnum $visible
 * @property Collection<int, Component> $components
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Meta> $meta
 *
 * @method static ComponentGroupFactory factory($count = null, $state = [])
 */
class ComponentGroup extends Model implements Metable
{
    /** @use HasFactory<ComponentGroupFactory> */
    use HasFactory;

    use HasMeta;
    use HasVisibility;

    /** @var array<string, string> */
    protected $casts = [
        'order' => 'int',
        'order_column' => ResourceOrderColumnEnum::class,
        'order_direction' => ResourceOrderDirectionEnum::class,
        'collapsed' => ComponentGroupVisibilityEnum::class,
        'visible' => ResourceVisibilityEnum::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'order',
        'order_column',
        'order_direction',
        'collapsed',
        'visible',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ComponentGroup $componentGroup): void {
            $componentGroup->components()->update(['component_group_id' => null]);
        });
    }

    /**
     * Get the components in the group.
     *
     * @return HasMany<Component, $this>
     */
    public function components(): HasMany
    {
        return $this->hasMany(Component::class)->chaperone('group');
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

    /**
     * Get the most severe status among the group's components.
     */
    public function worstComponentStatus(): ComponentStatusEnum
    {
        return $this->components
            ->map(fn (Component $component) => ($component->incidents_count ?? 0) > 0
                ? $component->latest_status
                : $component->status)
            ->sortByDesc(fn (ComponentStatusEnum $status) => $status->severity())
            ->first() ?? ComponentStatusEnum::operational;
    }

    public function hasActiveIncident(): bool
    {
        if ($this->components->every(fn (Component $component) => $component->hasAttribute('incidents_count'))) {
            return $this->components->contains(fn (Component $component) => $component->incidents_count > 0);
        }

        return Incident::query()
            ->unresolved()
            ->whereHas('components', fn ($query) => $query->whereIn('components.id', $this->components->pluck('id')))
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
