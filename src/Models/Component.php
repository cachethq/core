<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\ComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Events\Components\ComponentUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $component_group_id
 * @property string $name
 * @property string $description
 * @property string $link
 * @property ComponentStatusEnum $status
 * @property int $order
 * @property bool $enabled
 * @property array $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * Relationships
 * @property ?ComponentGroup $group
 * @property Collection<array-key, Incident> $incidents
 * @property Collection<array-key, Subscriber> $subscribers
 *
 * Methods
 *
 * @method static ComponentFactory factory($count = null, $state = [])
 */
class Component extends Model
{
    use HasFactory, SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'status' => ComponentStatusEnum::class,
        'order' => 'int',
        'enabled' => 'bool',
        'meta' => 'json',
    ];

    /** @var array<array-key, string> */
    protected $fillable = [
        'name',
        'description',
        'link',
        'order',
        'status',
        'component_group_id',
        'enabled',
        'meta',
    ];

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => ComponentCreated::class,
        'deleted' => ComponentDeleted::class,
        'updated' => ComponentUpdated::class,
    ];

    /**
     * Get the group the component belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ComponentGroup::class, 'component_group_id', 'id');
    }

    /**
     * Get the incidents for the component.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Get the subscribers for this component.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'subscriptions');
    }

    /**
     * Scope to disabled components only.
     */
    public function scopeDisabled(Builder $query): Builder
    {
        return $query->where('enabled', false);
    }

    /**
     * Scope to enabled components only.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to a specific status.
     */
    public function scopeStatus(Builder $query, ComponentStatusEnum $status): Builder
    {
        return $query->where('status', $status);
    }
}
