<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleComponentFactory;
use Cachet\Enums\ComponentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $schedule_id
 * @property int $component_id
 * @property ?ComponentStatusEnum $component_status
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Schedule $schedule
 * @property Component $component
 *
 * @method static ScheduleComponentFactory factory($count = null, $state = [])
 */
class ScheduleComponent extends Pivot
{
    protected $table = 'schedule_components';

    /** @use HasFactory<ScheduleComponentFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'component_status' => ComponentStatusEnum::class,
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
