<?php

namespace Cachet\Models;

use Cachet\Cachet;
use Cachet\Concerns\HasMeta;
use Cachet\Concerns\HasTags;
use Cachet\Concerns\Metable;
use Cachet\Database\Factories\ComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Events\Components\ComponentUpdated;
use Cachet\QueryBuilders\ScheduleBuilder;
use Cachet\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property ?string $description
 * @property ?string $link
 * @property ComponentStatusEnum $status
 * @property ComponentStatusEnum $latest_status
 * @property-read ?Incident $latest_unresolved_incident
 * @property-read ?Incident $impacting_incident
 * @property ?int $order
 * @property ?int $component_group_id
 * @property ?bool $checked
 * @property ?Carbon $checked_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property bool $enabled
 * @property ?ComponentGroup $componentGroup
 * @property-read Collection<int, ComponentCheck> $checks
 * @property-read Collection<int, Meta> $meta
 * @property-read IncidentComponent|null $pivot
 *
 * @method static Builder<static>|static checked()
 * @method static Builder<static>|static disabled()
 * @method static Builder<static>|static enabled()
 * @method static Builder<static>|static outage()
 * @method static Builder<static>|static status(ComponentStatusEnum $status)
 * @method static ComponentFactory factory($count = null, $state = [])
 */
class Component extends Model implements Metable
{
    /** @use HasFactory<ComponentFactory> */
    use HasFactory;

    use HasMeta;
    use HasTags;
    use SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'checked' => 'bool',
        'status' => ComponentStatusEnum::class,
        'order' => 'int',
        'enabled' => 'bool',
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
        'checked',
        'checked_at',
    ];

    protected $dispatchesEvents = [
        'created' => ComponentCreated::class,
        'deleted' => ComponentDeleted::class,
        'updated' => ComponentUpdated::class,
    ];

    /**
     * Keep the cached status-page aggregates in step with component changes.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Status::flush());
        static::deleted(fn () => Status::flush());
    }

    /**
     * Render the Markdown description.
     */
    public function formattedDescription(): string
    {
        return Cachet::markdown($this->description, inline: true);
    }

    /**
     * Get the link to render on the status page, which is only ever an http
     * or https URL.
     */
    public function formattedLink(): ?string
    {
        return Str::isUrl((string) $this->link, ['http', 'https'])
            ? $this->link
            : null;
    }

    /**
     * Get the group the component belongs to.
     *
     * @return BelongsTo<ComponentGroup, $this>
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
     * Get the unresolved incidents publicly affecting the component, newest first.
     *
     * Embargoed and non-public incidents are excluded: an incident that nobody
     * can read must not change what the status page shows.
     *
     * @return BelongsToMany<Incident, $this, IncidentComponent>
     */
    public function unresolvedIncidents(): BelongsToMany
    {
        return $this->incidents()->unresolved()->viewableBy(false)->latest();
    }

    /**
     * Get the published maintenance windows currently in progress for the component.
     *
     * @return BelongsToMany<Schedule, $this, ScheduleComponent>
     */
    public function activeMaintenance(): BelongsToMany
    {
        $relation = $this->schedules()->published();

        /** @var ScheduleBuilder $query */
        $query = $relation->getQuery();

        $query->inProgress();

        return $relation;
    }

    /**
     * Get the schedules for the component.
     *
     * @return BelongsToMany<Schedule, $this, ScheduleComponent>
     */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_components')
            ->using(ScheduleComponent::class)
            ->withTimestamps()
            ->withPivot('component_status');
    }

    /**
     * Get the recorded monitoring checks for the component.
     *
     * @return HasMany<ComponentCheck, $this>
     */
    public function checks(): HasMany
    {
        return $this->hasMany(ComponentCheck::class);
    }

    /**
     * Get the recorded changes to the component's baseline status.
     *
     * @return HasMany<ComponentStatusChange, $this>
     */
    public function statusChanges(): HasMany
    {
        return $this->hasMany(ComponentStatusChange::class);
    }

    /**
     * Get the subscribers for this component.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'subscriptions');
    }

    /**
     * Scope to checked components only.
     */
    public function scopeChecked(Builder $query): void
    {
        $query->where('checked', true);
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
     * Get the latest unresolved incident affecting the component.
     *
     * Uses the eager-loaded relation when available to avoid a query per component.
     */
    public function latestUnresolvedIncident(): Attribute
    {
        return Attribute::get(fn () => $this->relationLoaded('unresolvedIncidents')
            ? $this->unresolvedIncidents->first()
            : $this->unresolvedIncidents()->first())->shouldCache();
    }

    /**
     * Get the effective status for the component — what the status page shows.
     *
     * The `status` column is the baseline: what operators and monitoring assert
     * about the component itself. Incidents and maintenance windows contribute
     * impacts on top of it without ever writing to it, and the most severe of
     * those wins. A maintenance window in progress replaces the baseline rather
     * than competing with it, so expected downtime during the window does not
     * surface as a real outage — an incident raised during it still does.
     */
    public function latestStatus(): Attribute
    {
        return Attribute::get(function (): ComponentStatusEnum {
            $baseline = $this->activeMaintenanceImpact()
                ?? $this->status;

            return $this->activeIncidentImpacts()
                ->push($baseline)
                ->sortByDesc(fn (ComponentStatusEnum $status) => $status->severity())
                ->first();
        })->shouldCache();
    }

    /**
     * Get the incident responsible for the component's effective status, if any.
     *
     * The most severe impact wins and the most recent breaks a tie. Null when
     * no incident is responsible — the baseline or a maintenance window may
     * outrank every impact — so the badge never links to an incident that
     * contradicts the status it is displaying.
     */
    public function impactingIncident(): Attribute
    {
        return Attribute::get(function (): ?Incident {
            $incident = $this->activeIncidents()
                ->filter(fn (Incident $incident) => $incident->pivot->component_status !== null)
                ->sortByDesc(fn (Incident $incident) => [
                    $incident->pivot->component_status->severity(),
                    $incident->created_at,
                ])
                ->first();

            return $incident?->pivot->component_status === $this->latest_status ? $incident : null;
        })->shouldCache();
    }

    /**
     * Determine whether a published maintenance window is currently in progress.
     */
    public function isUnderMaintenance(): bool
    {
        if ($this->relationLoaded('activeMaintenance')) {
            return $this->activeMaintenance->isNotEmpty();
        }

        return $this->activeMaintenance()->exists();
    }

    /**
     * The status the component takes on while maintenance is in progress.
     *
     * Each window says what its components should look like while it runs, and
     * the most severe of any overlapping windows wins. Null when no window is
     * in progress, in which case the baseline stands.
     */
    protected function activeMaintenanceImpact(): ?ComponentStatusEnum
    {
        $schedules = $this->relationLoaded('activeMaintenance')
            ? $this->activeMaintenance
            : $this->activeMaintenance()->get();

        if ($schedules->isEmpty()) {
            return null;
        }

        return $schedules
            ->map(fn (Schedule $schedule) => $schedule->pivot->component_status)
            ->filter()
            ->sortByDesc(fn (ComponentStatusEnum $status) => $status->severity())
            ->first() ?? ComponentStatusEnum::under_maintenance;
    }

    /**
     * The statuses imposed on this component by the incidents affecting it.
     *
     * @return SupportCollection<int, ComponentStatusEnum>
     */
    protected function activeIncidentImpacts(): SupportCollection
    {
        return $this->activeIncidents()
            ->map(fn (Incident $incident) => $incident->pivot->component_status)
            ->filter()
            ->values();
    }

    /**
     * The unresolved, publicly visible incidents affecting this component.
     *
     * Uses the eager-loaded relation when available to avoid a query per component.
     *
     * @return Collection<int, Incident>
     */
    protected function activeIncidents(): Collection
    {
        return $this->relationLoaded('unresolvedIncidents')
            ? $this->unresolvedIncidents
            : $this->unresolvedIncidents()->get();
    }

    /**
     * Determine how to order the component.
     */
    public function orderableBy(ComponentGroup $group): mixed
    {
        return match ($group->order_column) {
            ResourceOrderColumnEnum::Id => $this->id,
            ResourceOrderColumnEnum::LastUpdated => $this->updated_at,
            ResourceOrderColumnEnum::Name => $this->name,
            ResourceOrderColumnEnum::Manual => $this->order,
            default => $this->latest_status->severity(),
        };
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ComponentFactory::new();
    }
}
