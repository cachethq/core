<?php

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
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
     * Get the incident the component is attached to.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the incident the component is attached to.
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
