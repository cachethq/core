<?php

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;

it('can delete an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    app(DeleteUpdate::class)->handle($update);

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $update->updateable_id,
    ]);
});

it('can delete a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    app(DeleteUpdate::class)->handle($update);

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Schedule::class),
        'updateable_id' => $update->updateable_id,
    ]);
});

it('restores the incident baseline status when deleting its last status-bearing update', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        'baseline_status' => IncidentStatusEnum::investigating,
    ]);

    $update = $incident->updates()->create([
        'status' => IncidentStatusEnum::identified,
        'message' => 'Investigating further.',
    ]);

    app(SyncIncidentStatus::class)->handle($incident);

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::identified);

    app(DeleteUpdate::class)->handle($update);

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::investigating);
});
