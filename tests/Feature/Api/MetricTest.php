<?php

use Cachet\Models\Metric;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can list metrics', function () {
    Metric::factory(2)->create();

    $response = getJson('/status/api/metrics');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 metrics by default', function () {
    Metric::factory(20)->create();

    $response = getJson('/status/api/metrics');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 metrics', function () {
    Metric::factory(20)->create();

    $response = getJson('/status/api/metrics?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can get a metric', function () {
    $metric = Metric::factory()->create();

    $response = getJson('/status/api/metrics/'.$metric->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $metric->id,
    ]);
});

it('can create a metric', function () {
    $response = postJson('/status/api/metrics', [
        'name' => 'New Metric',
        'suffix' => 'cups of tea',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'New Metric',
    ]);
    $this->assertDatabaseHas('metrics', [
        'name' => 'New Metric',
        'suffix' => 'cups of tea',
    ]);
});
