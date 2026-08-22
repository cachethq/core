<?php

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $incident_id
 * @property int $component_id
 * @property ?ComponentStatusEnum $component_status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Incident $incident
 * @property Component $component
 *
 * @method static IncidentComponentFactory factory($count = null, $state = [])
 */
class IncidentComponent extends Pivot
{
    protected $table = 'incident_components';

    /** @use HasFactory<IncidentComponentFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'component_status' => ComponentStatusEnum::class,
    ];

    /**
     * Attaching or detaching a component changes its effective status, so the
     * cached status-page aggregates are flushed alongside.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Status::flush());
        static::deleted(fn () => Status::flush());
    }

    /**
     * Get the incident the component is attached to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the incident this component belongs to.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return IncidentComponentFactory::new();
    }
}
