<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleComponentFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $schedule_id
 * @property int $component_id
 * @property int $component_status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Schedule $schedule
 * @property Component $component
 *
 * @method static ScheduleComponentFactory factory($count = null, $state = [])
 */
class ScheduleComponent extends Model
{
    /** @use HasFactory<ScheduleComponentFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'schedule_id',
        'component_id',
        'component_status',
    ];

    /**
     * Get the affected component.
     *
     * @retun BelongsTo<Component, $this>
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    /**
     * Get the schedule this schedule-component belongs to.
     *
     * @return BelongsTo<Schedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ScheduleComponentFactory::new();
    }
}
