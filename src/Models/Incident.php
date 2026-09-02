<?php

namespace Cachet\Models;

use Cachet\Cachet;
use Cachet\Concerns\HasMeta;
use Cachet\Concerns\HasTags;
use Cachet\Concerns\HasVisibility;
use Cachet\Concerns\Metable;
use Cachet\Concerns\Publishable;
use Cachet\Database\Factories\IncidentFactory;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Status;
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
use Illuminate\Support\Facades\Cache;
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
 * @property ?Carbon $published_at
 * @property ?Carbon $published_notified_at
 * @property ?Carbon $long_running_notified_at
 * @property ?int $user_id
 * @property int $notifications
 * @property string $guid
 * @property ?string $external_provider
 * @property ?string $external_id
 * @property ?TUser $user
 * @property ?Component $component
 * @property Collection<int, Component> $components
 * @property Collection<int, Update> $updates
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Meta> $meta
 * @property-read Carbon $timestamp
 * @property-read IncidentComponent $pivot
 *
 * @method static IncidentFactory factory($count = null, $state = [])
 * @method static Builder<static>|static status(IncidentStatusEnum $status)
 * @method static Builder<static>|static unresolved()
 * @method static Builder<static>|static stickied()
 * @method static Builder<static>|static published()
 * @method static Builder<static>|static unpublished()
 */
class Incident extends Model implements Metable
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    use HasMeta;
    use HasTags;
    use HasVisibility;
    use Publishable;
    use SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'status' => IncidentStatusEnum::class,
        'visible' => ResourceVisibilityEnum::class,
        'stickied' => 'bool',
        'scheduled_at' => 'datetime',
        'occurred_at' => 'datetime',
        'published_at' => 'datetime',
        'published_notified_at' => 'datetime',
        'long_running_notified_at' => 'datetime',
    ];

    /** @var array<string, class-string> */
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
        'published_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Incident $model) {
            $model->guid = Str::uuid();

            if ($model->published_at === null) {
                $model->published_at = $model->freshTimestamp();
                $model->published_notified_at = $model->freshTimestamp();
            }
        });

        self::saved(function (): void {
            self::forgetRssFeed();
            Status::flush();
        });

        self::deleted(function (): void {
            self::forgetRssFeed();
            Status::flush();
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
        return $this->hasMany(IncidentComponent::class)->chaperone();
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
        $query->whereIn($this->qualifyColumn('status'), IncidentStatusEnum::unresolved());
    }

    /**
     * Scope to the incidents a given viewer is allowed to see.
     *
     * Publication (when it may be seen) and visibility (who may see it) are
     * orthogonal, and this is the single place both are decided. Every read
     * surface — the API, MCP, RSS, the status page and the system status —
     * goes through it so none can forget one of the two.
     *
     * @param  Builder<static>  $query
     */
    public function scopeViewableBy(Builder $query, bool $authenticated, bool $includeUnpublished = false): void
    {
        $query->visible($authenticated)
            ->unless($includeUnpublished, fn (Builder $query) => $query->published());
    }

    /**
     * Determine whether a given viewer is allowed to see this incident.
     *
     * The visibility attribute is read through `getAttribute()` because Eloquent
     * itself declares a `$visible` property for serialisation, which would
     * otherwise shadow the column when read from inside the model.
     */
    public function isViewableBy(bool $authenticated, bool $includeUnpublished = false): bool
    {
        $permitted = $authenticated
            ? ResourceVisibilityEnum::visibleToUsers()
            : ResourceVisibilityEnum::visibleToGuests();

        if (! in_array($this->getAttribute('visible'), $permitted, true)) {
            return false;
        }

        return $includeUnpublished || $this->isPublished();
    }

    /**
     * Scope to stickied incidents.
     */
    public function scopeStickied(Builder $query): void
    {
        $query->where('stickied', true);
    }

    public function scopeOccursAfter(Builder $query, $date): void
    {
        $query->where('occurred_at', '>=', $date);
    }

    public function scopeOccursBefore(Builder $query, $date): void
    {
        $query->where('occurred_at', '<=', $date);
    }

    public function scopeOccursOn(Builder $query, $date): void
    {
        $query->whereDate('occurred_at', $date);
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
     * The incident's status.
     *
     * Retained for backwards compatibility: the status column is canonical and
     * is kept in step with the latest status-bearing update at write time, so
     * this is simply an alias for it.
     *
     * @return Attribute<IncidentStatusEnum|null, never>
     */
    protected function latestStatus(): Attribute
    {
        return Attribute::make(get: fn (): ?IncidentStatusEnum => $this->status);
    }

    /**
     * Clear the cached RSS feed and its HTTP validators.
     */
    private static function forgetRssFeed(): void
    {
        Cache::forget('cachet::rss-feed');
        Cache::forget('cachet::rss-feed-last-modified');
    }

    /**
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Cachet::markdown($this->message);
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
