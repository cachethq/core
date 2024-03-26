<?php

namespace Cachet\Models;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Events\Incidents\IncidentUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => IncidentStatusEnum::class,
        'visible' => ResourceVisibilityEnum::class,
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

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Incident $model) {
            $model->guid = Str::uuid();
        });
    }

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('cachet.user_model'));
    }

    /**
     * Scope to a specific status.
     */
    public function scopeStatus(Builder $query, IncidentStatusEnum $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->whereIn('status', IncidentStatusEnum::unresolved());
    }

    /**
     * Scope to stickied incidents.
     */
    public function scopeStickied(Builder $query): Builder
    {
        return $query->where('stickied', true);
    }

    public function timestamp(): Attribute
    {
        return Attribute::get(fn () => $this->occurred_at ?: $this->created_at);
    }

    /**
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Str::of($this->message)->markdown();
    }
}
