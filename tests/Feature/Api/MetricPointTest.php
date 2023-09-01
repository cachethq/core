<?php

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;

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

it('can get a metric point', function () {
    $metricPoint = MetricPoint::factory()->forMetric()->create();

    $response = getJson('/status/api/metrics/'.$metricPoint->metric_id.'/points/'.$metricPoint->id.'?per_page=18');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $metricPoint->id,
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
