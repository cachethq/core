<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Models\Component;
use Illuminate\Support\Facades\DB;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentRequestData $component): Component
    {
        return DB::transaction(function () use ($component): Component {
            return tap(Component::create($component->except('meta')->toArray()), function (Component $model) use ($component) {
                $model->syncMeta($component->meta ?? []);
            });
        });
    }
}
