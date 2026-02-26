<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Verbs\Events\Components\ComponentCreated;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentRequestData $data): Component
    {
        return ComponentCreated::commit(
            name: $data->name,
            status: $data->status ?? ComponentStatusEnum::operational,
            description: $data->description,
            link: $data->link,
            order: $data->order ?? 0,
            component_group_id: $data->componentGroupId,
            enabled: $data->enabled,
            meta: [],
        );
    }
}
