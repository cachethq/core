<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Enums\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $name
 * @property string $message
 * @property ScheduleStatusEnum $status
 * @property Carbon $scheduled_at
 * @property Carbon $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<array-key, Component> $components
 */
class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => ScheduleStatusEnum::class,
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'message',
        'status',
        'scheduled_at',
        'completed_at',
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

    /**
     * Scope schedules that are incomplete.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereIn('status', [ScheduleStatusEnum::incomplete()])
            ->whereNull('completed_at');
    }

    /**
     * Scope schedules that are in progress.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('scheduled_at', '<=', Carbon::now())
            ->where('status', '=', ScheduleStatusEnum::in_progress)
            ->whereNull('completed_at');
    }

    /**
     * Scopes schedules to those in the future.
     */
    public function scopeInTheFuture(Builder $query): Builder
    {
        return $query->whereIn('status', ScheduleStatusEnum::upcoming())
            ->whereDate('scheduled_at', '>=', Carbon::now());
    }

    /**
     * Scopes schedules to those scheduled in the past.
     */
    public function scopeInThePast(Builder $query): Builder
    {
        return $query->whereIn('status', ScheduleStatusEnum::upcoming())
            ->where('scheduled_at', '<=', Carbon::now());
    }

    /**
     * Scopes schedules to those completed in the past.
     */
    public function scopeCompletedPreviously(Builder $query): Builder
    {
        return $query->where('status', '=', ScheduleStatusEnum::complete)
            ->where('completed_at', '<=', Carbon::now());
    }
}
