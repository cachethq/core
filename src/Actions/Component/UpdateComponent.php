<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Models\Component;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class UpdateComponent
{
    public function __construct(private ChangeComponentStatus $changeComponentStatus)
    {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(Component $component, UpdateComponentRequestData $data, ?Authenticatable $user = null): Component
    {
        DB::transaction(function () use ($component, $data, $user): void {
            $attributes = $data->except('meta', 'status')->toArray();

            if ($data->status instanceof ComponentStatusEnum) {
                $this->changeComponentStatus->handle(
                    $component,
                    $data->status,
                    ComponentStatusSourceEnum::Manual,
                    $user,
                    attributes: $attributes,
                );
            } else {
                $component->update($attributes);
            }

            if (is_array($data->meta)) {
                $component->syncMeta($data->meta);
            }
        });

        return $component->fresh();
    }
}
