<?php

declare(strict_types=1);

namespace Cachet\Models;

use App\Models\User;
use Cachet\Database\Factories\IncidentFactory;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Events\Incidents\IncidentUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $guid
 * @property int $user_id
 * @property int $component_id
 * @property string $name
 * @property string $message
 * @property IncidentStatusEnum $status
 * @property bool $visible
 * @property bool $stickied
 * @property array $notifications
 * @property Carbon $scheduled_at
 * @property Carbon $occurred_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * Relationships
 * @property ?User $user
 * @property Component $component
 * @property Collection<array-key, Component> $components
 * @property Collection<array-key, IncidentUpdate> $incidentUpdates
 *
 * Methods
 *
 * @method static IncidentFactory factory($count = null, $state = [])
 */
class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => IncidentStatusEnum::class,
        'visible' => 'bool',
        'stickied' => 'bool',
        'scheduled_at' => 'datetime',
        'occurred_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => IncidentCreated::class,
        'deleted' => IncidentDeleted::class,
        'updated' => IncidentUpdated::class,
    ];

    protected $fillable = [
        'guid',
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

    /**
     * Get the components impacted by this incident.
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'incident_components')
            ->withPivot(['status_id']);
    }

    /**
     * Get the updates for this incident.
     */
    public function incidentUpdates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class);
    }

    /**
     * Get the user that created the incident.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Scope to a specific status.
     */
    public function scopeStatus(Builder $query, IncidentStatusEnum $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to stickied incidents.
     */
    public function scopeStickied(Builder $query): Builder
    {
        return $query->where('stickied', true);
    }
}
