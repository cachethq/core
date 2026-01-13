<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Models\Schedule;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ScheduleUpdated extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    public function __construct(
        int $schedule_id,
        public ?string $name = null,
        public ?string $message = null,
        public ?string $scheduled_at = null,
        public ?string $completed_at = null,
    ) {
        $this->schedule_id = $schedule_id;
    }

    public function validate(ScheduleState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ScheduleState $state): void
    {
        if ($this->name !== null) {
            $state->name = $this->name;
        }
        if ($this->message !== null) {
            $state->message = $this->message;
        }
        if ($this->scheduled_at !== null) {
            $state->scheduled_at = $this->scheduled_at;
        }
        if ($this->completed_at !== null) {
            $state->completed_at = $this->completed_at;
        }
    }

    public function handle(ScheduleState $state): Schedule
    {
        $schedule = Schedule::findOrFail($this->schedule_id);

        $updates = array_filter([
            'name' => $this->name,
            'message' => $this->message,
            'scheduled_at' => $this->scheduled_at,
            'completed_at' => $this->completed_at,
        ], fn ($value) => $value !== null);

        if (! empty($updates)) {
            $schedule->update($updates);
        }

        return $schedule->fresh();
    }
}
