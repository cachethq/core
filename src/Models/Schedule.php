<?php

namespace Cachet\Models;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filament\Resources\ScheduleResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
     * Render the Markdown message.
     */
    public function formattedMessage(): string
    {
        return Str::of($this->message)->markdown();
    }

    /**
     * Scope schedules that are incomplete.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereIn('status', ScheduleStatusEnum::incomplete())
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

    /**
     * Get the URL to the schedule page within the dashboard.
     */
    public function filamentDashboardEditUrl(): string
    {
        return ScheduleResource::getUrl(name: 'edit', parameters: ['record' => $this->id]);
    }

    public function timestamp(): Attribute
    {
        return Attribute::get(fn () => $this->completed_at ?: $this->scheduled_at);
    }
}
