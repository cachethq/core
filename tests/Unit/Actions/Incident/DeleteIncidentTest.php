<?php

use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Models\Incident;
use Illuminate\Support\Facades\Event;

it('can create delete an incident', function () {
    Event::fake();

    $incident = Incident::factory()->create();

    DeleteIncident::run($incident);

    expect(Incident::find($incident->id))->toBeNull();

    Event::assertDispatched(IncidentDeleted::class, fn ($event) => $event->incident->is($incident));
});

it('deletes related incident updates', function () {
    $incident = Incident::factory()->hasIncidentUpdates(2)->create();

    DeleteIncident::run($incident);

    expect(Incident::find($incident->id))->toBeNull();
    $this->assertDatabaseMissing('incident_updates', [
        'incident_id' => $incident->id,
    ]);
});
