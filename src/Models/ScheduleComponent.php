<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Properties
 *
 * @property-read int $id
 * @property int $schedule_id
 * @property int $component_id
 * @property ComponentStatusEnum $component_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property ?Schedule $schedule
 * @property ?Component $component
 *
 * Methods
 *
 * @method static ScheduleComponentFactory factory($count = null, $state = [])
 */
class ScheduleComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'component_id',
        'component_status',
    ];

    /**
     * Get the affected component.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the schedule the schedule component belongs to.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
