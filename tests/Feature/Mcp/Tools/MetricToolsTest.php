<?php

use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\MetricPoints\AddMetricPoint;
use Cachet\Mcp\Tools\MetricPoints\DeleteMetricPoint;
use Cachet\Mcp\Tools\Metrics\CreateMetric;
use Cachet\Mcp\Tools\Metrics\DeleteMetric;
use Cachet\Mcp\Tools\Metrics\GetMetric;
use Cachet\Mcp\Tools\Metrics\ListMetrics;
use Cachet\Mcp\Tools\Metrics\UpdateMetric;
use Cachet\Models\Metric;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\assertDatabaseHas;

it('lists metrics for guests', function () {
    Metric::factory(2)->create();

    CachetServer::tool(ListMetrics::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('gets a metric with recent points', function () {
    $metric = Metric::factory()->create(['name' => 'Response Time']);
    $metric->metricPoints()->create(['value' => 42.5]);

    CachetServer::tool(GetMetric::class, ['id' => $metric->id])
        ->assertOk()
        ->assertSee('Response Time');
});

it('returns an error for an unknown metric', function () {
    CachetServer::tool(GetMetric::class, ['id' => 999])
        ->assertHasErrors(['Metric [999] not found.']);
});

it('creates a metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    CachetServer::tool(CreateMetric::class, [
        'name' => 'API Latency',
        'suffix' => 'ms',
    ])->assertOk()->assertSee('API Latency');

    assertDatabaseHas('metrics', ['name' => 'API Latency']);
});

it('updates a metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.manage']);

    $metric = Metric::factory()->create(['suffix' => 'ms']);

    CachetServer::tool(UpdateMetric::class, [
        'id' => $metric->id,
        'suffix' => 'seconds',
    ])->assertOk();

    expect($metric->fresh()->suffix)->toBe('seconds');
});

it('deletes a metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metrics.delete']);

    $metric = Metric::factory()->create();

    CachetServer::tool(DeleteMetric::class, ['id' => $metric->id])
        ->assertOk();

    expect(Metric::query()->find($metric->id))->toBeNull();
});

it('adds a metric point', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    CachetServer::tool(AddMetricPoint::class, [
        'metric_id' => $metric->id,
        'value' => 128.5,
    ])->assertOk();

    expect($metric->metricPoints()->count())->toBe(1);
});

it('deletes a metric point', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.delete']);

    $metric = Metric::factory()->create();
    $point = $metric->metricPoints()->create(['value' => 10]);

    CachetServer::tool(DeleteMetricPoint::class, [
        'metric_id' => $metric->id,
        'metric_point_id' => $point->id,
    ])->assertOk();

    expect($metric->metricPoints()->count())->toBe(0);
});

it('scopes metric points to the metric', function () {
    Sanctum::actingAs(User::factory()->create(), ['metric-points.delete']);

    $metric = Metric::factory()->create();
    $other = Metric::factory()->create();
    $point = $other->metricPoints()->create(['value' => 10]);

    CachetServer::tool(DeleteMetricPoint::class, [
        'metric_id' => $metric->id,
        'metric_point_id' => $point->id,
    ])->assertHasErrors(["Metric point [{$point->id}] not found on metric [{$metric->id}]."]);
});
