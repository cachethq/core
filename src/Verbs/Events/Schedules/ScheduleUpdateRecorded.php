<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ScheduleUpdateRecorded extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    public function __construct(
        int $schedule_id,
        public string $message,
        public ?IncidentStatusEnum $status = null,
        public ?int $user_id = null,
    ) {
        $this->schedule_id = $schedule_id;
    }

    public function validate(ScheduleState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ScheduleState $state): void
    {
        $state->updates[] = [
            'status' => $this->status,
            'message' => $this->message,
            'user_id' => $this->user_id,
            'at' => now()->toIso8601String(),
        ];
    }

    public function handle(ScheduleState $state): Update
    {
        $schedule = Schedule::findOrFail($this->schedule_id);

        $update = new Update([
            'status' => $this->status,
            'message' => $this->message,
            'user_id' => $this->user_id,
        ]);

        $schedule->updates()->save($update);

        return $update;
    }
}
