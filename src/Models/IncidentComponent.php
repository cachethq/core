<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentComponentFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $component_id
 * @property int $incident_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Component $component
 * @property Incident $incident
 *
 * Methods
 *
 * @method static IncidentComponentFactory factory($count = null, $state = [])
 */
class IncidentComponent extends Model
{
    use HasFactory;

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
}
