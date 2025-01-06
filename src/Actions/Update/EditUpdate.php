<?php

namespace Cachet\Actions\Update;

use Cachet\Data\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Data\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Models\Update;

class EditUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Update $update, EditIncidentUpdateRequestData|EditScheduleUpdateRequestData $data): Update
    {
        return tap($update, function (Update $update) use ($data) {
            $update->update($data->toArray());
        });
    }
}
