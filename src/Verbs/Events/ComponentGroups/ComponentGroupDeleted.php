<?php

namespace Cachet\Verbs\Events\ComponentGroups;

use Cachet\Models\ComponentGroup;
use Cachet\Verbs\States\ComponentGroupState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentGroupDeleted extends Event
{
    #[StateId(ComponentGroupState::class)]
    public int $component_group_id;

    public function __construct(int $component_group_id)
    {
        $this->component_group_id = $component_group_id;
    }

    public function validate(ComponentGroupState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ComponentGroupState $state): void
    {
        $state->deleted = true;
    }

    public function handle(): void
    {
        $group = ComponentGroup::findOrFail($this->component_group_id);
        $group->delete();
    }
}
