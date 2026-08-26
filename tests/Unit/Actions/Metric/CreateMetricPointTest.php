<?php

use Cachet\Actions\Metric\CreateMetricPoint;
use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('creates a metric point if it is the first point', function () {
    $metric = Metric::factory()->create();

    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from([
        'value' => 1,
    ]));

    expect($point)
        ->toBeInstanceOf(MetricPoint::class)
        ->created_at->toBeInstanceOf(DateTime::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1,
        'sum_value' => 1,
        'counter' => 1,
    ]);
    $this->assertDatabaseCount('metric_points', 1);
});

it('creates a metric point with a default value', function () {
    $metric = Metric::factory()->create([
        'default_value' => 1234,
    ]);

    $point = app(CreateMetricPoint::class)->handle($metric);

    expect($point)->toBeInstanceOf(MetricPoint::class);
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'value' => 1234,
        'sum_value' => 1234,
        'counter' => 1,
    ]);
    $this->assertDatabaseCount('metric_points', 1);
});

it('accumulates into the bucket if within the metric\'s threshold', function () {
    Carbon::setTestNow('2026-07-25 12:01:00');

    $metric = Metric::factory()->create(['threshold' => 5]);

    app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 4]));

    Carbon::setTestNow('2026-07-25 12:03:00');

    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 6]));

    // The second value is added to the bucket rather than thrown away, and
    // "value" holds the reading that arrived most recently.
    expect($point->value)->toBe(6.0)
        ->and($point->sum_value)->toBe(10.0)
        ->and($point->counter)->toBe(2)
        ->and($point->created_at->toDateTimeString())->toBe('2026-07-25 12:00:00');
    $this->assertDatabaseCount('metric_points', 1);
});

it('creates a metric point if it is outside of the metric\'s threshold', function () {
    Carbon::setTestNow('2026-07-25 12:01:00');

    $metric = Metric::factory()->create(['threshold' => 5]);

    app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 4]));

    Carbon::setTestNow('2026-07-25 12:06:00');

    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 6]));

    expect($point->sum_value)->toBe(6.0)
        ->and($point->counter)->toBe(1)
        ->and($point->created_at->toDateTimeString())->toBe('2026-07-25 12:05:00');
    $this->assertDatabaseCount('metric_points', 2);
});

it('resolves a sum metric to the total of its observations', function () {
    $metric = Metric::factory()->create([
        'calc_type' => MetricTypeEnum::sum,
        'threshold' => 5,
    ]);

    Carbon::setTestNow('2026-07-25 12:01:00');
    app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 4]));
    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 6]));

    expect($point->calculated_value)->toBe(10.0);
});

it('resolves an average metric to the mean of its observations', function () {
    $metric = Metric::factory()->create([
        'calc_type' => MetricTypeEnum::average,
        'threshold' => 5,
    ]);

    Carbon::setTestNow('2026-07-25 12:01:00');
    app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 4]));
    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 6]));

    expect($point->calculated_value)->toBe(5.0);
});

it('no longer dispatches a created event per observation', function () {
    Event::fake();

    $metric = Metric::factory()->create();

    app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from(['value' => 1]));

    // Points are written with an accumulating upsert, which loads no model
    // and so fires no model events. Counter increments never fired one
    // either, so consumers only ever saw an arbitrary subsample.
    Event::assertNotDispatched(MetricPointCreated::class);
});

it('creates a metric point for a given timestamp', function ($timestamp) {
    $metric = Metric::factory()->create(['threshold' => 1]);

    $point = app(CreateMetricPoint::class)->handle($metric, CreateMetricPointRequestData::from([
        'value' => 1,
        'timestamp' => $timestamp,
    ]));

    expect($point)
        ->toBeInstanceOf(MetricPoint::class)
        ->created_at->toDateTimeString()->toBe(Carbon::parse($timestamp)->toDateTimeString());
})->with([
    now()->addWeek()->startOfMinute()->unix(),
    now()->subHour()->startOfMinute()->toAtomString(),
    now()->subHour()->startOfMinute()->toDateTime(),
    now()->subHour()->startOfMinute()->toDateTimeString(),
]);

it('rounds created_at into 30 second intervals', function (string $timestamp, string $expected) {
    $metric = Metric::factory()->create();

    Carbon::setTestNow(Carbon::parse($timestamp));

    $point = $metric->metricPoints()->create(['value' => 1, 'created_at' => $timestamp]);

    expect($point->created_at)->toDateTimeString()->toBe($expected);
})->with([
    ['2023-09-13 12:34:15', '2023-09-13 12:34:30'],
    ['2023-09-13 12:34:45', '2023-09-13 12:35:00'],
]);
