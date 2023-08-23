<?php

use Cachet\Models\Incident;

it('can have multiple components', function () {
    $incident = Incident::factory()->hasComponents(2)->create();

    expect($incident->components)->toHaveCount(2);
});
