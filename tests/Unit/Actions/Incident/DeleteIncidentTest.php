<?php

use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Models\Incident;
use Illuminate\Support\Facades\Event;

it('can create delete an incident', function () {
    Event::fake();

    $incident = Incident::factory()->create();

    app(DeleteIncident::class)->handle($incident);

    $this->assertSoftDeleted('incidents', [
        'id' => $incident->id,
    ]);

    Event::assertDispatched(IncidentDeleted::class, fn ($event) => $event->incident->is($incident));
});

it('deletes related incident updates', function () {
    $incident = Incident::factory()->hasIncidentUpdates(2)->create();

    app(DeleteIncident::class)->handle($incident);

    $this->assertSoftDeleted('incidents', [
        'id' => $incident->id,
    ]);
    $this->assertDatabaseMissing('incident_updates', [
        'incident_id' => $incident->id,
    ]);
});
