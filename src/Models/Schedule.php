<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleFactory;
use Cachet\Enums\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property ?string $message
 * @property Carbon $scheduled_at
 * @property ?Carbon $completed_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property Collection<int, Component> $components
 * @property Collection<int, Update> $updates
 * @property-read ScheduleStatusEnum $status
 *
 * @method static ScheduleFactory factory($count = null, $state = [])
 * @method static Builder<Schedule> incomplete()
 * @method static Builder<Schedule> inProgress()
 * @method static Builder<Schedule> inTheFuture()
 * @method static Builder<Schedule> inThePast()
 */
class Schedule extends Model
{
    /** @use HasFactory<ScheduleFactory> */
    use HasFactory;
    use SoftDeletes;

    /** @var array<string, string> */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'message',
        'scheduled_at',
        'completed_at',
    ];

    /**
     * Get the status of the schedule.
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                $now = Carbon::now();

                return match (true) {
                    $this->scheduled_at->gte($now) => ScheduleStatusEnum::upcoming,
                    $this->completed_at === null => ScheduleStatusEnum::in_progress,
                    default => ScheduleStatusEnum::complete,
                };
            }
        );
    }

    /**
     * Get the components affected by this schedule.
     *
     * @return BelongsToMany<Component, $this>
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(
            Component::class,
            'schedule_components',
        );
    }

    /**
     * Get the updates for this schedule.
     *
     * @return MorphMany<Update, $this>
     */
    public function updates(): MorphMany
    {
        return $this->morphMany(Update::class, 'updateable')->chaperone();
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
     *
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', '>=', Carbon::now())
            ->whereNull('completed_at');
    }

    /**
     * Scope schedules that are in progress.
     *
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', '<=', Carbon::now())
            ->where(function (Builder $query) {
                $query->whereDate('completed_at', '>=', Carbon::now())
                    ->orWhereNull('completed_at');
            });
    }

    /**
     * Scopes schedules to those in the future.
     *
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeInTheFuture(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', '>=', Carbon::now());
    }

    /**
     * Scopes schedules to those scheduled in the past.
     *
     * @param Builder<$this> $query
     * @return Builder<$this>
     */
    public function scopeInThePast(Builder $query): Builder
    {
        return $query->where('completed_at', '<=', Carbon::now());
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ScheduleFactory::new();
    }
}
