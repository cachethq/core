<?php

namespace Cachet\Models;

use Cachet\Concerns\HasVisibility;
use Cachet\Database\Factories\ComponentGroupFactory;
use Cachet\Enums\ComponentGroupVisibilityEnum;
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
 * @property ?array<string, mixed> $meta
 * @property Collection<int, Component> $components
 *
 * @method static ComponentGroupFactory factory($count = null, $state = [])
 */
class ComponentGroup extends Model
{
    /** @use HasFactory<ComponentGroupFactory> */
    use HasFactory;

    use HasVisibility;

    /** @var array<string, string> */
    protected $casts = [
        'order' => 'int',
        'collapsed' => ComponentGroupVisibilityEnum::class,
        'visible' => ResourceVisibilityEnum::class,
        'meta' => 'json',
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'order',
        'collapsed',
        'visible',
        'meta',
    ];

    /**
     * Get the components in the group.
     *
     * @return HasMany<Component, $this>
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
