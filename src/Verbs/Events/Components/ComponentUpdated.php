<?php

namespace Cachet\Verbs\Events\Components;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentUpdated as WebhookComponentUpdated;
use Cachet\Models\Component;
use Cachet\Verbs\States\ComponentGroupState;
use Cachet\Verbs\States\ComponentState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;
use Thunk\Verbs\Facades\Verbs;

class ComponentUpdated extends Event
{
    #[StateId(ComponentState::class)]
    public int $component_id;

    public function __construct(
        int $component_id,
        public ?string $name = null,
        public ?ComponentStatusEnum $status = null,
        public ?string $description = null,
        public ?string $link = null,
        public ?int $order = null,
        public ?int $component_group_id = null,
        public bool $clear_component_group = false,
        public ?bool $enabled = null,
        public ?array $meta = null,
    ) {
        $this->component_id = $component_id;
    }

    public function validate(ComponentState $state): bool
    {
        return ! (isset($state->deleted) && $state->deleted);
    }

    public function apply(ComponentState $state): void
    {
        $oldGroupId = isset($state->component_group_id) ? $state->component_group_id : null;

        if ($this->name !== null) {
            $state->name = $this->name;
        }
        if ($this->description !== null) {
            $state->description = $this->description;
        }
        if ($this->link !== null) {
            $state->link = $this->link;
        }
        if ($this->status !== null) {
            $previousStatus = isset($state->status) ? $state->status : null;
            if ($previousStatus !== $this->status) {
                $state->status_history[] = [
                    'status' => $this->status,
                    'at' => now()->toIso8601String(),
                ];
                $state->status = $this->status;
            }
        }
        if ($this->order !== null) {
            $state->order = $this->order;
        }
        if ($this->component_group_id !== null) {
            $state->component_group_id = $this->component_group_id;
        }
        if ($this->clear_component_group) {
            $state->component_group_id = null;
        }
        if ($this->enabled !== null) {
            $state->enabled = $this->enabled;
        }
        if ($this->meta !== null) {
            $state->meta = $this->meta;
        }

        // Update group membership if group changed
        $newGroupId = $this->clear_component_group ? null : $this->component_group_id;
        if (($newGroupId !== null || $this->clear_component_group) && $oldGroupId !== $newGroupId) {
            // Remove from old group
            if ($oldGroupId) {
                $oldGroupState = ComponentGroupState::load($oldGroupId);
                $oldGroupState->component_ids = array_values(
                    array_filter($oldGroupState->component_ids ?? [], fn ($id) => $id !== $this->component_id)
                );
            }
            // Add to new group
            if ($newGroupId) {
                $newGroupState = ComponentGroupState::load($newGroupId);
                if (! in_array($this->component_id, $newGroupState->component_ids ?? [])) {
                    $newGroupState->component_ids[] = $this->component_id;
                }
            }
        }
    }

    public function handle(ComponentState $state): Component
    {
        $component = Component::findOrFail($this->component_id);

        $updates = array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'status' => $this->status,
            'order' => $this->order,
            'component_group_id' => $this->component_group_id,
            'enabled' => $this->enabled,
            'meta' => $this->meta,
        ], fn ($value) => $value !== null);

        // Handle explicit clearing of component group
        if ($this->clear_component_group) {
            $updates['component_group_id'] = null;
        }

        if (! empty($updates)) {
            $component->update($updates);
        }

        Verbs::unlessReplaying(fn () => event(new WebhookComponentUpdated($component)));

        return $component->fresh();
    }
}
