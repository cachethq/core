<?php

namespace Cachet\Models;

use Cachet\Concerns\HasVisibility;
use Cachet\Database\Factories\IncidentFactory;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @template TUser of Authenticatable
 *
 * @property int $id
 * @property ?int $component_id
 * @property string $name
 * @property ?IncidentStatusEnum $status
 * @property string $message
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property ResourceVisibilityEnum $visible
 * @property bool $stickied
 * @property ?Carbon $occurred_at
 * @property ?int $user_id
 * @property int $notifications
 * @property string $guid
 * @property ?string $external_provider
 * @property ?string $external_id
 * @property ?TUser $user
 * @property ?Component $component
 * @property Collection<int, Component> $components
 * @property Collection<int, Update> $updates
 * @property-read Carbon $timestamp
 * @property-read IncidentComponent $pivot
 *
 * @method static IncidentFactory factory($count = null, $state = [])
 * @method static Builder<static>|static status(IncidentStatusEnum $status)
 * @method static Builder<static>|static unresolved()
 * @method static Builder<static>|static stickied()
 */
class Incident extends Model
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    use HasVisibility;
    use SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'status' => IncidentStatusEnum::class,
        'visible' => ResourceVisibilityEnum::class,
        'stickied' => 'bool',
        'scheduled_at' => 'datetime',
        'occurred_at' => 'datetime',
    ];

    /** @var array<string, string> */
    protected $dispatchesEvents = [
        'created' => IncidentCreated::class,
        'deleted' => IncidentDeleted::class,
        'updated' => IncidentUpdated::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'guid',
        'external_provider',
        'external_id',
        'user_id',
        'component_id',
        'name',
        'status',
        'visible',
        'stickied',
        'notifications',
        'message',
        'scheduled_at',
        'occurred_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Incident $model) {
            $model->guid = Str::uuid();
        });
    }

    /**
     * Get the components impacted by this incident.
     *
     * @return BelongsToMany<Component, $this, IncidentComponent>
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'incident_components')
            ->using(IncidentComponent::class)
            ->withTimestamps()
            ->withPivot(['component_status']);
    }

    /**
     * Get the impacted components for this incident.
     */
    public function incidentComponents(): HasMany
    {
        return $this->hasMany(IncidentComponent::class);
    }

    /**
     * Get the updates for this incident.
     *
     * @return MorphMany<Update, $this>
     */
    public function updates(): MorphMany
    {
        return $this->morphMany(Update::class, 'updateable')->chaperone();
    }

    /**
     * Get the user who reported the incident.
     */
    public function user(): BelongsTo
    {
        $userModel = config('cachet.user_model');

        return $this->belongsTo($userModel);
    }

    /**
     * Scope to a specific status.
     */
    public function scopeStatus(Builder $query, IncidentStatusEnum $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope to unresolved incidents.
     */
    public function scopeUnresolved(Builder $query): void
    {
        $query->whereIn('status', IncidentStatusEnum::unresolved());
    }

    /**
     * Scope to stickied incidents.
     */
    public function scopeStickied(Builder $query): void
    {
        $query->where('stickied', true);
    }

    /**
     * @return Attribute<Carbon, never>
     */
    protected function timestamp(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->occurred_at ?: $this->created_at
        );
    }

    /**
     * Determine the latest status of the incident.
     *
     * @return Attribute<IncidentStatusEnum|null, never>
     */
    protected function latestStatus(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->updates()->latest()->first()->status ?? $this->status;
            }
        );
    }

    /**
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Str::of($this->message)->markdown();
    }

    /**
     * Get the URL to the incident page within the dashboard.
     */
    public function filamentDashboardEditUrl(): string
    {
        return IncidentResource::getUrl(name: 'edit', parameters: ['record' => $this->id]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return IncidentFactory::new();
    }
}
