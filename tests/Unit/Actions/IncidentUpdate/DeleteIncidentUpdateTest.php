<?php

use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;

it('can delete an incident update', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    app(DeleteUpdate::class)->handle($incidentUpdate);

    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incidentUpdate->updateable_id,
    ]);
});
