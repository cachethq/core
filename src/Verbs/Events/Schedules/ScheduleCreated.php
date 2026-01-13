<?php

namespace Cachet\Verbs\Events\Schedules;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Verbs\States\ComponentState;
use Cachet\Verbs\States\ScheduleState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ScheduleCreated extends Event
{
    #[StateId(ScheduleState::class)]
    public ?int $schedule_id = null;

    /**
     * @param  array<int, array{id: int, status: ComponentStatusEnum}>  $components
     */
    public function __construct(
        public string $name,
        public ?string $message = null,
        public ?string $scheduled_at = null,
        public ?string $completed_at = null,
        public array $components = [],
    ) {}

    public function apply(ScheduleState $state): void
    {
        $state->id = $this->schedule_id;
        $state->name = $this->name;
        $state->message = $this->message;
        $state->scheduled_at = $this->scheduled_at;
        $state->completed_at = $this->completed_at;
        $state->deleted = false;

        foreach ($this->components as $component) {
            $state->affected_components[$component['id']] = $component['status'];

            $componentState = ComponentState::load($component['id']);
            if (! in_array($this->schedule_id, $componentState->schedule_ids)) {
                $componentState->schedule_ids[] = $this->schedule_id;
            }
        }
    }

    public function handle(ScheduleState $state): Schedule
    {
        if (config('verbs.migration_mode', false)) {
            return Schedule::find($this->schedule_id);
        }

        $schedule = Schedule::create([
            'name' => $this->name,
            'message' => $this->message,
            'scheduled_at' => $this->scheduled_at,
            'completed_at' => $this->completed_at,
        ]);

        $this->schedule_id = $schedule->id;

        if (! empty($this->components)) {
            $pivotData = collect($this->components)->mapWithKeys(fn ($c) => [
                $c['id'] => ['component_status' => $c['status']],
            ])->all();
            $schedule->components()->sync($pivotData);
        }

        return $schedule;
    }
}
