<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Illuminate\Support\Facades\DB;

/**
 * Run the backfill over whatever rows are present.
 */
function backfillIncidentStatuses(): void
{
    $migration = require __DIR__.'/../../../database/migrations/2026_07_25_000003_backfill_incident_status_from_updates.php';

    $migration->up();
}

/**
 * Write a status straight to the column, as the pre-canonical code would have left it.
 */
function staleIncident(IncidentStatusEnum $status): Incident
{
    $incident = Incident::factory()->create();

    DB::table('incidents')->where('id', $incident->id)->update(['status' => $status->value]);

    return $incident->refresh();
}

it('aligns an incident with its newest status-bearing update', function () {
    $incident = staleIncident(IncidentStatusEnum::investigating);

    $incident->updates()->create(['status' => IncidentStatusEnum::identified, 'message' => 'Found it.']);
    $incident->updates()->create(['status' => IncidentStatusEnum::watching, 'message' => 'Monitoring.']);

    backfillIncidentStatuses();

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::watching);
});

it('leaves an incident with no status-bearing updates alone', function () {
    $incident = staleIncident(IncidentStatusEnum::investigating);

    $incident->updates()->create(['status' => null, 'message' => 'A note.']);

    backfillIncidentStatuses();

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::investigating);
});

it('does not disturb the timestamps of the incidents it aligns', function () {
    $incident = staleIncident(IncidentStatusEnum::investigating);

    $incident->updates()->create(['status' => IncidentStatusEnum::fixed, 'message' => 'Done.']);

    $updatedAt = $incident->fresh()->updated_at;

    backfillIncidentStatuses();

    expect($incident->fresh())
        ->status->toBe(IncidentStatusEnum::fixed)
        ->updated_at->toEqual($updatedAt);
});

it('backfills many incidents at once', function () {
    $incidents = collect([IncidentStatusEnum::identified, IncidentStatusEnum::watching, IncidentStatusEnum::fixed])
        ->map(function (IncidentStatusEnum $status) {
            $incident = staleIncident(IncidentStatusEnum::investigating);
            $incident->updates()->create(['status' => $status, 'message' => 'Update.']);

            return [$incident, $status];
        });

    backfillIncidentStatuses();

    $incidents->each(fn (array $pair) => expect($pair[0]->fresh()->status)->toBe($pair[1]));
});
