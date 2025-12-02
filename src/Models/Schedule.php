<?php

namespace Cachet\Models;

use Cachet\Database\Factories\ScheduleFactory;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\QueryBuilders\ScheduleBuilder;
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
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property ?string $message
 * @property ?Carbon $scheduled_at
 * @property ?Carbon $completed_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 * @property Collection<int, Component> $components
 * @property Collection<int, Update> $updates
 *
 * @method static ScheduleFactory factory($count = null, $state = [])
 * @method static ScheduleBuilder incomplete()
 * @method static ScheduleBuilder inProgress()
 * @method static ScheduleBuilder inTheFuture()
 * @method static ScheduleBuilder inThePast()
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
     *
     * @return Attribute<ScheduleStatusEnum, never>
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                $now = Carbon::now();

                return match (true) {
                    $this->scheduled_at->gte($now) => ScheduleStatusEnum::upcoming,
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
     * @return BelongsToMany<Component, $this>
     */
    public function components(): BelongsToMany
    {
        return $this->belongsToMany(
            Component::class,
            'schedule_components',
        )->withPivot(['component_status'])
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
        return Str::of($this->message)->markdown();
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
