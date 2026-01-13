<?php

namespace Cachet\Verbs\Events\ComponentGroups;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\ComponentGroup;
use Cachet\Verbs\States\ComponentGroupState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentGroupCreated extends Event
{
    #[StateId(ComponentGroupState::class)]
    public ?int $component_group_id = null;

    public function __construct(
        public string $name,
        public int $order = 0,
        public ComponentGroupVisibilityEnum $collapsed = ComponentGroupVisibilityEnum::expanded,
        public ResourceVisibilityEnum $visible = ResourceVisibilityEnum::guest,
    ) {}

    public function apply(ComponentGroupState $state): void
    {
        $state->id = $this->component_group_id;
        $state->name = $this->name;
        $state->order = $this->order;
        $state->collapsed = $this->collapsed;
        $state->visible = $this->visible;
        $state->deleted = false;
        $state->component_ids = [];
    }

    public function handle(ComponentGroupState $state): ComponentGroup
    {
        if (config('verbs.migration_mode', false)) {
            return ComponentGroup::find($this->component_group_id);
        }

        $group = ComponentGroup::create([
            'name' => $this->name,
            'order' => $this->order,
            'collapsed' => $this->collapsed,
            'visible' => $this->visible,
        ]);

        $this->component_group_id = $group->id;

        return $group;
    }
}
