<?php

use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Models\Incident;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;

it('can delete an incident', function () {
    Event::fake();

    $incident = Incident::factory()->create();

    app(DeleteIncident::class)->handle($incident);

    $this->assertSoftDeleted('incidents', [
        'id' => $incident->id,
    ]);

    Event::assertDispatched(IncidentDeleted::class, fn ($event) => $event->incident->is($incident));
});

it('deletes attached incident updates', function () {
    $incident = Incident::factory()->hasUpdates(2)->create();

    app(DeleteIncident::class)->handle($incident);

    $this->assertSoftDeleted('incidents', [
        'id' => $incident->id,
    ]);
    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incident->id,
    ]);
});
