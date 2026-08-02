<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Support\Facades\DB;

class DeleteUpdate
{
    public function __construct(private SyncIncidentStatus $syncIncidentStatus)
    {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(Update $update): void
    {
        DB::transaction(function () use ($update): void {
            $incident = $update->updateable;

            $update->delete();

            if ($incident instanceof Incident) {
                $this->syncIncidentStatus->handle($incident);
            }
        });
    }
}
