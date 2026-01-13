<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Models\Schedule;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ScheduleCompleted extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    public function __construct(
        int $schedule_id,
        public string $completed_at,
    ) {
        $this->schedule_id = $schedule_id;
    }

    public function validate(ScheduleState $state): bool
    {
        $isDeleted = isset($state->deleted) && $state->deleted;
        $isCompleted = isset($state->completed_at) && $state->completed_at !== null;

        return ! $isDeleted && ! $isCompleted;
    }

    public function apply(ScheduleState $state): void
    {
        $state->completed_at = $this->completed_at;
    }

    public function handle(ScheduleState $state): Schedule
    {
        $schedule = Schedule::findOrFail($this->schedule_id);

        $schedule->update(['completed_at' => $this->completed_at]);

        return $schedule->fresh();
    }
}
