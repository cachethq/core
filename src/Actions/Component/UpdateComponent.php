<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\UpdateComponentRequestData;
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
            $component->update($data->except('meta', 'status')->toArray());

            if ($data->meta !== null) {
                $component->syncMeta($data->meta);
            }

            if ($data->status !== null) {
                $this->changeComponentStatus->handle(
                    $component,
                    $data->status,
                    ComponentStatusSourceEnum::Manual,
                    $user,
                );
            }
        });

        return $component->fresh();
    }
}
