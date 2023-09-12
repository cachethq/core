<?php

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Support\Facades\Event;

it('creates a metric point if it is the first point', function () {
    Event::fake();

    $metric = Metric::factory()->create();

    $point = CreateMetricPoint::run($metric, [
        'value' => 1,
    ]);

    expect($point)
        ->toBeInstanceOf(MetricPoint::class)
        ->created_at->toBeInstanceOf(\DateTime::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1,
        'counter' => 1,
    ]);
    $this->assertDatabaseCount('metric_points', 1);
    Event::assertDispatched(MetricPointCreated::class);
});

it('creates a metric point with a default value', function () {
    Event::fake();
    $metric = Metric::factory()->create([
        'default_value' => 1234,
    ]);

    $point = CreateMetricPoint::run($metric);

    expect($point)->toBeInstanceOf(MetricPoint::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1234,
        'counter' => 1,
    ]);
    $this->assertDatabaseCount('metric_points', 1);
    Event::assertDispatched(MetricPointCreated::class);
});

it('increments the counter if within the metric\'s threshold', function () {
    Event::fake();
    $metric = Metric::factory()->hasMetricPoints(1, [
        'created_at' => now()->startOfMinute(),
    ])->create([
        'threshold' => 1,
    ]);

    $point = CreateMetricPoint::run($metric, [
        'value' => 1,
    ]);

    expect($point)->toBeInstanceOf(MetricPoint::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1,
        'counter' => 2,
    ]);
    $this->assertDatabaseCount('metric_points', 1);
    Event::assertDispatched(MetricPointCreated::class);
});

it('creates a metric point if it is outside of the metric\'s threshold', function () {
    Event::fake();
    $metric = Metric::factory()->hasMetricPoints(1, [
        'created_at' => now()->addMinutes(5),
    ])->create([
        'threshold' => 1,
    ]);

    $point = CreateMetricPoint::run($metric, [
        'value' => 1,
    ]);

    expect($point)->toBeInstanceOf(MetricPoint::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1,
        'counter' => 1,
    ]);
    $this->assertDatabaseCount('metric_points', 2);
    Event::assertDispatched(MetricPointCreated::class);
});

it('creates a metric point for a given timestamp', function ($timestamp) {
    Event::fake();
    $metric = Metric::factory()->hasMetricPoints(1, [
        'created_at' => now()->subHour()->startOfMinute(),
    ])->create([
        'threshold' => 1,
    ]);

    $point = CreateMetricPoint::run($metric, [
        'value' => 1,
        'timestamp' => $timestamp,
    ]);

    expect($point)
        ->toBeInstanceOf(MetricPoint::class)
        ->created_at->isSameAs(Carbon\Carbon::parse($timestamp));
})->with([
    now()->addWeek()->startOfMinute()->unix(),
    now()->subHour()->startOfMinute()->toAtomString(),
    now()->subHour()->startOfMinute()->toDateTime(),
    now()->subHour()->startOfMinute()->toDateTimeString(),
]);
