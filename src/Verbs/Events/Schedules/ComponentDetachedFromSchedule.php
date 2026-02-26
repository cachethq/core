<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Models\Schedule;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentDetachedFromSchedule extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $schedule_id,
        int $component_id,
    ) {
        $this->schedule_id = $schedule_id;
        $this->component_id = $component_id;
    }

    public function validate(ScheduleState $scheduleState, ComponentState $componentState): bool
    {
        return ! $scheduleState->deleted
            && isset($scheduleState->affected_components[$this->component_id]);
    }

    public function apply(ScheduleState $scheduleState, ComponentState $componentState): void
    {
        unset($scheduleState->affected_components[$this->component_id]);

        $componentState->schedule_ids = array_values(
            array_filter($componentState->schedule_ids, fn ($id) => $id !== $this->schedule_id)
        );
    }

    public function handle(): void
    {
        $schedule = Schedule::findOrFail($this->schedule_id);
        $schedule->components()->detach($this->component_id);
    }
}
