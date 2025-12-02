<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Events\Components\ComponentUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property ?string $link
 * @property ?ComponentStatusEnum $status
 * @property ComponentStatusEnum $latest_status
 * @property ?int $order
 * @property ?int $component_group_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property bool $enabled
 * @property array<string, mixed> $meta
 * @property ?ComponentGroup $componentGroup
 * @property-read IncidentComponent|null $pivot
 *
 * @method static Builder<static>|static disabled()
 * @method static Builder<static>|static enabled()
 * @method static Builder<static>|static outage()
 * @method static Builder<static>|static status(ComponentStatusEnum $status)
 * @method static ComponentFactory factory($count = null, $state = [])
 */
class Component extends Model
{
    /** @use HasFactory<ComponentFactory> */
    use HasFactory;

    use SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'status' => ComponentStatusEnum::class,
        'order' => 'int',
        'enabled' => 'bool',
        'meta' => 'json',
    ];

    /** @var list<string> */
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
     *
     * @return BelongsToMany<Incident, $this, IncidentComponent>
     */
    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_components')
            ->using(IncidentComponent::class)
            ->withTimestamps()
            ->withPivot('component_status');
    }

    /**
     * Get the schedules for the component.
     *
     * @return BelongsToMany<Schedule, $this>
     */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_components')
            ->withTimestamps()
            ->withPivot('component_status');
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
    public function scopeDisabled(Builder $query): void
    {
        $query->where('enabled', false);
    }

    /**
     * Scope to enabled components only.
     */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    /**
     * Scope to a specific status.
     */
    public function scopeStatus(Builder $query, ComponentStatusEnum $status): void
    {
        $query->where('status', $status);
    }

    public function scopeOutage(Builder $query): void
    {
        $query->whereIn('status', ComponentStatusEnum::outage());
    }

    /**
     * Get the latest status for the component.
     */
    public function latestStatus(): Attribute
    {
        return Attribute::get(fn () => $this->incidents()->unresolved()->latest()->first()?->pivot->component_status ?? $this->status);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ComponentFactory::new();
    }
}
