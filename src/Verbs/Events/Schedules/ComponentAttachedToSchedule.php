<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentAttachedToSchedule extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $schedule_id,
        int $component_id,
        public ComponentStatusEnum $component_status,
    ) {
        $this->schedule_id = $schedule_id;
        $this->component_id = $component_id;
    }

    public function validate(ScheduleState $scheduleState, ComponentState $componentState): bool
    {
        return ! $scheduleState->deleted
            && ! $componentState->deleted
            && ! isset($scheduleState->affected_components[$this->component_id]);
    }

    public function apply(ScheduleState $scheduleState, ComponentState $componentState): void
    {
        $scheduleState->affected_components[$this->component_id] = $this->component_status;

        if (! in_array($this->schedule_id, $componentState->schedule_ids)) {
            $componentState->schedule_ids[] = $this->schedule_id;
        }
    }

    public function handle(): void
    {
        $schedule = Schedule::findOrFail($this->schedule_id);
        $schedule->components()->attach($this->component_id, [
            'component_status' => $this->component_status,
        ]);
    }
}
