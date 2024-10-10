<?php

use Cachet\Models\Incident;

it('can get the rss feed', function () {
    $incident = Incident::factory()->create();

    $this->get('/status/rss')
        ->assertOk()
        ->assertSee($incident->name);
});
