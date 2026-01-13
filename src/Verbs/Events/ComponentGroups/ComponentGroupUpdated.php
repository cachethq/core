<?php

namespace Cachet\Verbs\Events\ComponentGroups;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\ComponentGroup;
use Cachet\Verbs\States\ComponentGroupState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class ComponentGroupUpdated extends Event
{
    #[StateId(ComponentGroupState::class)]
    public int $component_group_id;

    public function __construct(
        int $component_group_id,
        public ?string $name = null,
        public ?int $order = null,
        public ?ComponentGroupVisibilityEnum $collapsed = null,
        public ?ResourceVisibilityEnum $visible = null,
    ) {
        $this->component_group_id = $component_group_id;
    }

    public function validate(ComponentGroupState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ComponentGroupState $state): void
    {
        if ($this->name !== null) {
            $state->name = $this->name;
        }
        if ($this->order !== null) {
            $state->order = $this->order;
        }
        if ($this->collapsed !== null) {
            $state->collapsed = $this->collapsed;
        }
        if ($this->visible !== null) {
            $state->visible = $this->visible;
        }
    }

    public function handle(ComponentGroupState $state): ComponentGroup
    {
        $group = ComponentGroup::findOrFail($this->component_group_id);

        $updates = array_filter([
            'name' => $this->name,
            'order' => $this->order,
            'collapsed' => $this->collapsed,
            'visible' => $this->visible,
        ], fn ($value) => $value !== null);

        if (! empty($updates)) {
            $group->update($updates);
        }

        return $group->fresh();
    }
}
