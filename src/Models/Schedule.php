<?php

namespace Cachet\Models;

use Cachet\Enums\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => ScheduleStatusEnum::class,
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the components affected by this schedule.
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(
            Component::class,
            'schedule_components',
        );
    }
}
