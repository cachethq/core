<?php

namespace Cachet\Verbs\Events\Components;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated as WebhookComponentCreated;
use Cachet\Models\Component;
use Cachet\Verbs\States\ComponentGroupState;
use Cachet\Verbs\States\ComponentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class ComponentCreated extends Event
{
    #[StateId(ComponentState::class)]
    public ?int $component_id = null;

    public function __construct(
        public string $name,
        public ComponentStatusEnum $status,
        public ?string $description = null,
        public ?string $link = null,
        public int $order = 0,
        public ?int $component_group_id = null,
        public bool $enabled = true,
        public array $meta = [],
    ) {}

    public function apply(ComponentState $state): void
    {
        $state->id = $this->component_id;
        $state->name = $this->name;
        $state->description = $this->description;
        $state->link = $this->link;
        $state->status = $this->status;
        $state->order = $this->order;
        $state->component_group_id = $this->component_group_id;
        $state->enabled = $this->enabled;
        $state->meta = $this->meta;
        $state->deleted = false;

        $state->status_history[] = [
            'status' => $this->status,
            'at' => now()->toIso8601String(),
        ];

        if ($this->component_group_id) {
            $groupState = ComponentGroupState::load($this->component_group_id);
            if (! in_array($this->component_id, $groupState->component_ids)) {
                $groupState->component_ids[] = $this->component_id;
            }
        }
    }

    public function handle(ComponentState $state): Component
    {
        if (config('verbs.migration_mode', false)) {
            return Component::find($this->component_id);
        }

        $component = Component::create([
            'name' => $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'status' => $this->status,
            'order' => $this->order,
            'component_group_id' => $this->component_group_id,
            'enabled' => $this->enabled,
            'meta' => $this->meta,
        ]);

        $this->component_id = $component->id;

        Verbs::unlessReplaying(fn () => event(new WebhookComponentCreated($component)));

        return $component;
    }
}
