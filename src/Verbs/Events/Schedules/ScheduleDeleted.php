<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Models\Schedule;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ScheduleDeleted extends Event
{
    #[StateId(ScheduleState::class)]
    public int $schedule_id;

    public function __construct(int $schedule_id)
    {
        $this->schedule_id = $schedule_id;
    }

    public function validate(ScheduleState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ScheduleState $state): void
    {
        $state->deleted = true;

        // Remove schedule from component states
        $affectedComponents = isset($state->affected_components) ? $state->affected_components : [];
        foreach (array_keys($affectedComponents) as $componentId) {
            $componentState = ComponentState::load($componentId);
            $componentState->schedule_ids = array_values(
                array_filter($componentState->schedule_ids ?? [], fn ($id) => $id !== $this->schedule_id)
            );
        }
    }

    public function handle(): void
    {
        $schedule = Schedule::findOrFail($this->schedule_id);

        // Delete related updates first
        $schedule->updates()->delete();

        $schedule->delete();
    }
}
