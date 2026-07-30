<?php

namespace Cachet\Actions\Incident;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;

class SyncIncidentStatus
{
    /**
     * Align an incident's status with its most recent status-bearing update.
     *
     * The incident's own column is the canonical status: updates drive it at
     * write time rather than being consulted at read time. An incident with no
     * status-bearing updates keeps whatever status it was given directly.
     */
    public function handle(Incident $incident): Incident
    {
        $status = $incident->updates()
            ->whereNotNull('status')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('status');

        $status = $status === null
            ? $incident->baseline_status
            : ($status instanceof IncidentStatusEnum ? $status : IncidentStatusEnum::from((int) $status));

        if ($incident->status !== $status) {
            $incident->update(['status' => $status]);
        }

        return $incident;
    }
}
