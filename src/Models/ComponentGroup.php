<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\ComponentGroupFactory;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $name
 * @property int $order
 * @property ComponentGroupVisibilityEnum $visible
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<array-key, Component> $components
 *
 * Methods
 *
 * @method static ComponentGroupFactory factory($count = null, $state = [])
 */
class ComponentGroup extends Model
{
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'order' => 'int',
        'visible' => ComponentGroupVisibilityEnum::class,
    ];

    /** @var array<array-key, string> */
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
    public function scopeVisibility($query, ComponentGroupVisibilityEnum $visibility): Builder
    {
        return $query->where('visible', $visibility);
    }
}
