<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Illuminate\Support\Facades\DB;

class UpdateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup, UpdateComponentGroupRequestData $data): ComponentGroup
    {
        DB::transaction(function () use ($componentGroup, $data): void {
            $componentGroup->update($data->except('components', 'meta')->toArray());

            if (is_array($data->meta)) {
                $componentGroup->syncMeta($data->meta);
            }

            if (is_array($data->components) && $data->components !== []) {
                Component::query()->whereIn('id', $data->components)->update([
                    'component_group_id' => $componentGroup->id,
                ]);
            }
        });

        return $componentGroup->fresh();
    }
}
