<?php

use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Incidents\IncidentDeleted;
use Cachet\Models\Component;
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

it('keeps updates and component attachments when the incident is soft deleted', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->hasUpdates(2)->create();
    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::major_outage]);

    app(DeleteIncident::class)->handle($incident);

    $this->assertSoftDeleted('incidents', [
        'id' => $incident->id,
    ]);
    $this->assertDatabaseHas('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incident->id,
    ]);
    $this->assertDatabaseHas('incident_components', [
        'incident_id' => $incident->id,
    ]);
});

it('restores an incident with its updates intact', function () {
    $incident = Incident::factory()->hasUpdates(2)->create();

    app(DeleteIncident::class)->handle($incident);

    $incident->restore();

    expect($incident->updates()->count())->toBe(2);
});

it('purges updates and component attachments when the incident is hard deleted', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->hasUpdates(2)->create();
    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::major_outage]);

    $incident->forceDelete();

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incident->id,
    ]);
    $this->assertDatabaseMissing('incident_components', [
        'incident_id' => $incident->id,
    ]);
});
