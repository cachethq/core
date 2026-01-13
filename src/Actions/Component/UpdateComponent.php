<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Models\Component;
use Cachet\Verbs\Events\Components\ComponentStatusChanged;
use Cachet\Verbs\Events\Components\ComponentUpdated;

class UpdateComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component, UpdateComponentRequestData $data): Component
    {
        $oldStatus = $component->status;
        $hasStatusChange = $data->status !== null && $data->status !== $oldStatus;

        // Fire status change event if status is changing
        if ($hasStatusChange) {
            ComponentStatusChanged::commit(
                component_id: $component->id,
                old_status: $oldStatus,
                new_status: $data->status,
            );
        }

        // Fire general update event for other changes
        ComponentUpdated::commit(
            component_id: $component->id,
            name: $data->name,
            status: $hasStatusChange ? null : $data->status, // Skip status if already handled
            description: $data->description,
            link: $data->link,
            order: $data->order,
            component_group_id: $data->componentGroupId,
            enabled: $data->enabled,
        );

        // Refresh the original model with updated data
        $component->refresh();

        return $component;
    }
}
