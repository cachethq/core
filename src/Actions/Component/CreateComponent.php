<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Models\Component;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentRequestData $component): Component
    {
        return tap(Component::create($component->except('meta', 'tags')->toArray()), function (Component $model) use ($component) {
            $model->syncMeta($component->meta ?? []);
            $model->syncTags($component->tags);
        });
    }
}
