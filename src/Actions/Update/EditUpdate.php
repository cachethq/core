<?php

namespace Cachet\Actions\Update;

use Cachet\Data\IncidentUpdate\EditIncidentUpdateData;
use Cachet\Data\ScheduleUpdate\EditScheduleUpdateData;
use Cachet\Models\Update;

class EditUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Update $update, EditIncidentUpdateData|EditScheduleUpdateData $data): Update
    {
        return tap($update, function (Update $update) use ($data) {
            $update->update($data->toArray());
        });
    }
}
