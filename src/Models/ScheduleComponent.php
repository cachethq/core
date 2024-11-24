<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ScheduleFactory::new();
    }
}
