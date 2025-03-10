<?php

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
