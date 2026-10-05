<?php

namespace Cachet\Models;

use Cachet\Actions\Schedule\NotifyScheduleCompletedSubscribers;
use Cachet\Actions\Schedule\NotifyScheduleRescheduledSubscribers;
use Cachet\Cachet;
use Cachet\Concerns\HasMeta;
use Cachet\Concerns\HasTags;
use Cachet\Concerns\Metable;
use Cachet\Concerns\Publishable;
use Cachet\Database\Factories\ScheduleFactory;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\QueryBuilders\ScheduleBuilder;
use Cachet\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property ?string $message
 * @property ?Carbon $scheduled_at
 * @property ?Carbon $completed_at
 * @property ?Carbon $completed_notified_at
 * @property ?Carbon $published_at
 * @property ?Carbon $published_notified_at
 * @property bool $notifications
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property Collection<int, Component> $components
 * @property Collection<int, Update> $updates
 * @property-read ScheduleComponent|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Meta> $meta
 *
 * @method static ScheduleFactory factory($count = null, $state = [])
 * @method static ScheduleBuilder incomplete()
 * @method static ScheduleBuilder inProgress()
 * @method static ScheduleBuilder inTheFuture()
 * @method static ScheduleBuilder inThePast()
 * @method static ScheduleBuilder published()
 * @method static ScheduleBuilder unpublished()
 */
class Schedule extends Model implements Metable
{
    /** @use HasFactory<ScheduleFactory> */
    use HasFactory;

    use HasMeta;
    use HasTags;
    use Publishable;
    use SoftDeletes;

    /**
     * Notify subscribers when the schedule transitions to complete, or when its window moves.
     */
    protected static function booted(): void
    {
        self::creating(function (Schedule $schedule) {
            if ($schedule->published_at === null) {
                $schedule->published_at = $schedule->freshTimestamp();
                $schedule->published_notified_at = $schedule->freshTimestamp();
            }
        });

        self::saved(fn () => Status::flush());
        self::deleted(fn () => Status::flush());

        self::updated(function (Schedule $schedule) {
            if ($schedule->wasChanged('completed_at') && $schedule->status === ScheduleStatusEnum::complete) {
                app(NotifyScheduleCompletedSubscribers::class)->handle($schedule);

                return;
            }

            if ($schedule->wasChanged(['scheduled_at', 'completed_at'])) {
                app(NotifyScheduleRescheduledSubscribers::class)->handle(
                    $schedule,
                    $schedule->getOriginal('scheduled_at'),
                    $schedule->getOriginal('completed_at'),
                );
            }
        });
    }

    /** @var array<string, string> */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'completed_notified_at' => 'datetime',
        'published_at' => 'datetime',
        'published_notified_at' => 'datetime',
        'notifications' => 'bool',
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'message',
        'scheduled_at',
        'completed_at',
        'published_at',
        'notifications',
    ];

    /**
     * Get the status of the schedule.
     *
     * @return Attribute<ScheduleStatusEnum, never>
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                $now = Carbon::now();

                return match (true) {
                    $this->scheduled_at?->gt($now) === true => ScheduleStatusEnum::upcoming,
                    $this->completed_at === null,
                    $this->completed_at->gte($now) => ScheduleStatusEnum::in_progress,
                    default => ScheduleStatusEnum::complete,
                };
            }
        );
    }

    /**
     * Get the components affected by this schedule.
     *
     * @return BelongsToMany<Component, $this, ScheduleComponent>
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(
            Component::class,
            'schedule_components',
        )->using(ScheduleComponent::class)
            ->withPivot(['component_status'])
            ->withTimestamps();
    }

    /**
     * Get the schedule components pivot entries.
     *
     * @return HasMany<ScheduleComponent, $this>
     */
    public function scheduleComponents(): HasMany
    {
        return $this->hasMany(ScheduleComponent::class);
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
        return Cachet::markdown($this->message);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return ScheduleBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new ScheduleBuilder($query);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ScheduleFactory::new();
    }
}
