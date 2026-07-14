<?php

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Metric;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

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

it('sorts metrics by created at descending by default', function () {
    Metric::factory(5)->sequence(
        ['created_at' => '2020-01-01'],
        ['created_at' => '2021-01-01'],
        ['created_at' => '2022-01-01'],
        ['created_at' => '2023-01-01'],
        ['created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/metrics');

    $response->assertJsonPath('data.0.attributes.id', 5);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 2);
    $response->assertJsonPath('data.4.attributes.id', 1);
});

it('can sort metrics by ID', function () {
    Metric::factory(5)->create();

    $response = getJson('/status/api/metrics?sort=id');

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort metrics by name', function () {
    Metric::factory(5)->sequence(
        ['name' => 'c', 'created_at' => '2020-01-01'],
        ['name' => 'a', 'created_at' => '2021-01-01'],
        ['name' => 'b', 'created_at' => '2022-01-01'],
        ['name' => 'e', 'created_at' => '2023-01-01'],
        ['name' => 'd', 'created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/metrics?sort=name');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 3);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 5);
    $response->assertJsonPath('data.4.attributes.id', 4);
});

it('can sort metrics by order', function () {
    Metric::factory(5)->sequence(
        ['order' => 3, 'created_at' => '2020-01-01'],
        ['order' => 1, 'created_at' => '2021-01-01'],
        ['order' => 2, 'created_at' => '2022-01-01'],
        ['order' => 4, 'created_at' => '2023-01-01'],
        ['order' => 5, 'created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/metrics?sort=order');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 3);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can filter metrics by name', function () {
    Metric::factory(20)->create();
    $metric = Metric::factory()->create([
        'name' => 'Name to filter by.',
    ]);

    $query = http_build_query([
        'filter' => [
            'name' => 'Name to filter by.',
        ],
    ]);

    $response = getJson('/status/api/metrics?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $metric->id);
});

it('can filter metrics by calculation type', function () {
    Metric::factory(20)->create([
        'calc_type' => MetricTypeEnum::sum,
    ]);
    $metric = Metric::factory()->create([
        'calc_type' => MetricTypeEnum::average,
    ]);

    $query = http_build_query([
        'filter' => [
            'calc_type' => MetricTypeEnum::average->value,
        ],
    ]);

    $response = getJson('/status/api/metrics?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $metric->id);
});

it('can get a metric', function () {
    Metric::factory(5)->create();
    $metric = Metric::factory()->create();

    $response = getJson('/status/api/metrics/'.$metric->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $metric->id,
    ]);
});

it('cannot create a metric if not authenticated', function () {
    $response = postJson('/status/api/metrics', [
        'name' => 'New Metric',
        'suffix' => 'cups of tea',
    ]);

    $response->assertUnauthorized();
});

it('cannot create a metric without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/metrics', [
        'name' => 'New Metric',
        'suffix' => 'cups of tea',
    ]);

    $response->assertForbidden();
});

it('can create a metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

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

it('cannot update a metric if not authenticated', function () {
    $metric = Metric::factory()->create();

    $response = putJson('/status/api/metrics/'.$metric->id, [
        'name' => 'Updated Metric',
        'suffix' => 'cups of tea',
    ]);

    $response->assertUnauthorized();
});

it('cannot update a metric without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->create();

    $response = putJson('/status/api/metrics/'.$metric->id, [
        'name' => 'Updated Metric',
        'suffix' => 'cups of tea',
    ]);

    $response->assertForbidden();
});

it('can update a metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    $metric = Metric::factory()->create();

    $response = putJson('/status/api/metrics/'.$metric->id, [
        'name' => 'Updated Metric',
        'suffix' => 'cups of tea',
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('metrics', [
        'name' => 'Updated Metric',
        'suffix' => 'cups of tea',
    ]);
});

it('can update a metric threshold', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    $metric = Metric::factory()->create(['threshold' => 5]);

    $response = putJson('/status/api/metrics/'.$metric->id, [
        'name' => $metric->name,
        'threshold' => 30,
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('metrics', [
        'id' => $metric->id,
        'threshold' => 30,
    ]);
});

it('can get a metric with points included', function () {
    $metric = Metric::factory()->hasMetricPoints(3)->create();

    $response = getJson('/status/api/metrics/'.$metric->id.'?include=points');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $metric->id]);
    expect($response->json('included'))->toHaveCount(3);
});

it('can list metrics with points included', function () {
    Metric::factory()->hasMetricPoints(2)->create();

    $response = getJson('/status/api/metrics?include=points');

    $response->assertOk();
    expect($response->json('included'))->toHaveCount(2);
});

it('can update a metric without a name', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    $metric = Metric::factory()->create(['name' => 'Original Name']);

    $response = putJson('/status/api/metrics/'.$metric->id, [
        'suffix' => 'ms',
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('metrics', [
        'id' => $metric->id,
        'name' => 'Original Name',
        'suffix' => 'ms',
    ]);
});

it('cannot update a metric with bad data', function (array $payload) {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    $metric = Metric::factory()->create();

    $response = putJson('/status/api/metrics/'.$metric->id, $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => null, 'suffix' => null],
    fn () => ['name' => 123],
]);

it('cannot delete a metric if not authenticated', function () {
    $metric = Metric::factory()->hasMetricPoints(1)->create();

    $response = deleteJson('/status/api/metrics/'.$metric->id);

    $response->assertUnauthorized();
});

it('cannot delete a metric without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->hasMetricPoints(1)->create();

    $response = deleteJson('/status/api/metrics/'.$metric->id);

    $response->assertForbidden();
});

it('can delete metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.delete']);

    $metric = Metric::factory()->hasMetricPoints(1)->create();

    $response = deleteJson('/status/api/metrics/'.$metric->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('metrics', [
        'id' => $metric->id,
    ]);
    $this->assertDatabaseMissing('metric_points', [
        'metric_id' => $metric->id,
    ]);
});

it('does not list metrics hidden from guests', function () {
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/metrics');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('lists authenticated metrics to authenticated users but never hidden ones', function () {
    Sanctum::actingAs(User::factory()->create());

    Metric::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/metrics');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not show a hidden metric to guests', function () {
    $metric = Metric::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/metrics/'.$metric->id);

    $response->assertNotFound();
});

it('shows an authenticated metric to authenticated users', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);

    $response = getJson('/status/api/metrics/'.$metric->id);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.id', $metric->id);
});

it('lists authenticated metrics to callers presenting a bearer token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    Metric::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    Metric::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/metrics', ['Authorization' => 'Bearer '.$token]);

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});
