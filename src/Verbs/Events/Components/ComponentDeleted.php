<?php

namespace Cachet\Verbs\Events\Components;

use Cachet\Events\Components\ComponentDeleted as WebhookComponentDeleted;
use Cachet\Models\Component;
use Cachet\Verbs\States\ComponentGroupState;
use Cachet\Verbs\States\ComponentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class ComponentDeleted extends Event
{
    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(int $component_id)
    {
        $this->component_id = $component_id;
    }

    public function validate(ComponentState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ComponentState $state): void
    {
        $state->deleted = true;

        // Remove from group
        if (isset($state->component_group_id) && $state->component_group_id) {
            $groupState = ComponentGroupState::load($state->component_group_id);
            $groupState->component_ids = array_values(
                array_filter($groupState->component_ids, fn ($id) => $id !== $this->component_id)
            );
        }
    }

    public function handle(ComponentState $state): void
    {
        $component = Component::findOrFail($this->component_id);

        // Detach all subscribers before deleting
        $component->subscribers()->detach();

        Verbs::unlessReplaying(fn () => event(new WebhookComponentDeleted($component)));

        $component->delete();
    }
}
