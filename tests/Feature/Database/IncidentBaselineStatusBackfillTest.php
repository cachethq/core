<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Return the incidents table to the state it was in before the baseline status migration.
 */
function dropIncidentBaselineStatus(): void
{
    Schema::table('incidents', function (Blueprint $table) {
        $table->dropColumn('baseline_status');
    });
}

/**
 * Run the baseline status migration over whatever rows are present.
 */
function addIncidentBaselineStatus(): void
{
    $migration = require __DIR__.'/../../../database/migrations/2026_07_26_000001_add_baseline_status_to_incidents.php';

    $migration->up();
}

it('backfills the incident baseline status from the current status', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::watching]);

    dropIncidentBaselineStatus();
    addIncidentBaselineStatus();

    expect($incident->fresh()->baseline_status)->toBe(IncidentStatusEnum::watching);
});

it('backfills unknown when an incident has no current status', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::watching]);

    DB::table('incidents')->where('id', $incident->id)->update(['status' => null]);

    dropIncidentBaselineStatus();
    addIncidentBaselineStatus();

    expect($incident->fresh())
        ->status->toBeNull()
        ->baseline_status->toBe(IncidentStatusEnum::unknown);
});
