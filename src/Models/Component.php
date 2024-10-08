<?php

namespace Cachet\Models;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Events\Components\ComponentUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => ComponentStatusEnum::class,
        'order' => 'int',
        'enabled' => 'bool',
        'meta' => 'json',
    ];

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
     */
    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_components')->withPivot('status');
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
