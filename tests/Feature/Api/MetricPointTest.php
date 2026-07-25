<?php

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can list metric points', function () {
    $metric = Metric::factory()->hasMetricPoints(2)->create();

    $response = getJson('/status/api/metrics/'.$metric->id.'/points');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 metric points by default', function () {
    $metric = Metric::factory()->hasMetricPoints(20)->create();

    $response = getJson('/status/api/metrics/'.$metric->id.'/points');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 metric points', function () {
    $metric = Metric::factory()->hasMetricPoints(20)->create();

    $response = getJson('/status/api/metrics/'.$metric->id.'/points?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('sorts metric points by id by default', function () {
    Metric::factory(5)->hasMetricPoints(20)->create();
    $metric = Metric::factory()->hasMetricPoints(20)->create();

    $response = getJson("/status/api/metrics/{$metric->id}/points");

    $response->assertJsonPath('data.0.attributes.id', 101);
    $response->assertJsonPath('data.1.attributes.id', 102);
    $response->assertJsonPath('data.2.attributes.id', 103);
    $response->assertJsonPath('data.3.attributes.id', 104);
    $response->assertJsonPath('data.4.attributes.id', 105);
});

it('can sort metric points by value', function () {
    $metric = Metric::factory()->hasMetricPoints(3)->create();

    $response = getJson('/status/api/metrics/'.$metric->id.'/points?sort=value');

    $response->assertOk();
});

it('can get a metric point', function () {
    MetricPoint::factory(5)->forMetric()->create();
    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = getJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id.'?per_page=18');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $metricPoint->id,
    ]);
});

it('cannot create a metric point if not authenticated', function () {
    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 10,
    ]);

    $response->assertUnauthorized();
});

it('cannot create a metric point without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 10,
    ]);

    $response->assertForbidden();
});

it('can create a metric point', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 10,
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'value' => 10,
        'counter' => 1,
        'calculated_value' => 10,
    ]);
});

it('cannot create a metric point with an invalid timestamp', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 1,
        'timestamp' => 'not-a-timestamp',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('timestamp');
});

it('can create a metric point with a unix timestamp', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 1,
        'timestamp' => now()->subHour()->startOfMinute()->unix(),
    ]);

    $response->assertCreated();
});

it('cannot delete a metric point if not authenticated', function () {
    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = deleteJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id);

    $response->assertUnauthorized();
});

it('cannot delete a metric point without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = deleteJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id);

    $response->assertForbidden();
});

it('can delete a metric point', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.delete']);

    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = deleteJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('metric_points', [
        'metric_id' => $metricPoint->metric_id,
    ]);
});

it('does not list points of a metric hidden from guests', function () {
    $metric = Metric::factory()->hasMetricPoints(2)->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/metrics/'.$metric->id.'/points');

    $response->assertNotFound();
});

it('lists points of an authenticated metric to authenticated users', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->hasMetricPoints(2)->create(['visible' => ResourceVisibilityEnum::authenticated]);

    $response = getJson('/status/api/metrics/'.$metric->id.'/points');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not show a point of a metric hidden from guests', function () {
    $metric = Metric::factory()->hasMetricPoints(1)->create(['visible' => ResourceVisibilityEnum::hidden]);
    $point = $metric->metricPoints()->first();

    $response = getJson('/status/api/metrics/'.$metric->id.'/points/'.$point->id);

    $response->assertNotFound();
});

it('cannot batch create metric points without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points/batch', [
        'points' => [['value' => 1]],
    ]);

    $response->assertForbidden();
});

it('can batch create metric points', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create(['threshold' => 5]);

    $response = postJson('/status/api/metrics/'.$metric->id.'/points/batch', [
        'points' => [
            ['value' => 1, 'timestamp' => '2026-07-25 12:01:00'],
            ['value' => 2, 'timestamp' => '2026-07-25 12:03:00'],
            ['value' => 4, 'timestamp' => '2026-07-25 12:06:00'],
        ],
    ]);

    $response->assertCreated();

    // Two of the three observations share a bucket, so they are combined.
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment(['sum_value' => 3, 'counter' => 2]);
    $this->assertDatabaseCount('metric_points', 2);
});

it('rejects a batch without any points', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points/batch', ['points' => []]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('points');
});

it('rejects a batch larger than the configured maximum', function () {
    config()->set('cachet.metrics.max_batch_points', 2);

    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points/batch', [
        'points' => [['value' => 1], ['value' => 2], ['value' => 3]],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('points');
});

it('rejects a batch holding an invalid point', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points/batch', [
        'points' => [['value' => 1], ['value' => 'not-a-number']],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('points.1.value');
});

it('reports a point through its metric calculation type', function () {
    $metric = Metric::factory()
        ->hasMetricPoints(1, ['value' => 5, 'counter' => 4])
        ->create(['calc_type' => MetricTypeEnum::average]);

    $response = getJson('/status/api/metrics/'.$metric->id.'/points');

    $response->assertOk();
    $response->assertJsonPath('data.0.attributes.calculated_value', 5);
    $response->assertJsonPath('data.0.attributes.sum_value', 20);
});
