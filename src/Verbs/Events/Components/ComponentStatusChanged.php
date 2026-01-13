<?php

namespace Cachet\Verbs\Events\Components;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;
use Cachet\Verbs\States\ComponentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class ComponentStatusChanged extends Event
{
    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $component_id,
        public ComponentStatusEnum $old_status,
        public ComponentStatusEnum $new_status,
    ) {
        $this->component_id = $component_id;
    }

    public function validate(ComponentState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted) && $this->old_status !== $this->new_status;
    }

    public function apply(ComponentState $state): void
    {
        $state->status = $this->new_status;
        $state->status_history[] = [
            'status' => $this->new_status,
            'at' => now()->toIso8601String(),
        ];
    }

    public function handle(ComponentState $state): Component
    {
        $component = Component::findOrFail($this->component_id);

        $component->update(['status' => $this->new_status]);

        Verbs::unlessReplaying(fn () => event(new ComponentStatusWasChanged(
            $component,
            $this->old_status,
            $this->new_status
        )));

        return $component->fresh();
    }
}
