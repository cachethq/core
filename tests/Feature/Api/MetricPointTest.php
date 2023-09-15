<?php

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;

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
    $metric = Metric::factory()->hasMetricPoints(20)->create();

    $response = getJson("/status/api/metrics/{$metric->id}/points");

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can get a metric point', function () {
    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = getJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id.'?per_page=18');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $metricPoint->id,
    ]);
});

it('can create a metric point', function () {
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

it('can delete a metric point', function () {
    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = deleteJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('metric_points', [
        'metric_id' => $metricPoint->metric_id,
    ]);
});
