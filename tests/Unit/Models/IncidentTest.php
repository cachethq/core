<?php

use Cachet\Models\Incident;

it('can have multiple components', function () {
    // @todo fix the incident_components relationship.
    $incident = Incident::factory()->hasComponents(2)->create();

    expect($incident->components)->toHaveCount(2);
})->skip('Fix the incident_components relationship.');
