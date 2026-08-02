<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Illuminate\Support\Facades\DB;

class CreateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentGroupRequestData $data): ComponentGroup
    {
        return DB::transaction(function () use ($data): ComponentGroup {
            return tap(ComponentGroup::create(
                $data->except('components', 'meta')->toArray(),
            ), function (ComponentGroup $componentGroup) use ($data) {
                $componentGroup->syncMeta($data->meta ?? []);

                if (! $data->components) {
                    return;
                }

                Component::query()->whereIn('id', $data->components)->update([
                    'component_group_id' => $componentGroup->id,
                ]);
            });
        });
    }
}
