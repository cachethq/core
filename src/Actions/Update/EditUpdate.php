<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Data\Requests\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Support\Facades\DB;

class EditUpdate
{
    public function __construct(private SyncIncidentStatus $syncIncidentStatus)
    {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(Update $update, EditIncidentUpdateRequestData|EditScheduleUpdateRequestData $data): Update
    {
        return tap($update, function (Update $update) use ($data) {
            DB::transaction(function () use ($update, $data): void {
                $update->update($data->toArray());

                $incident = $update->updateable;

                if ($incident instanceof Incident) {
                    $this->syncIncidentStatus->handle($incident);
                }
            });
        });
    }
}
