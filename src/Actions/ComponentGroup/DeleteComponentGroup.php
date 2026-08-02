<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;
use Illuminate\Support\Facades\DB;

class DeleteComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup): void
    {
        DB::transaction(function () use ($componentGroup): void {
            $componentGroup->components()->update(['component_group_id' => null]);

            $componentGroup->delete();
        });
    }
}
