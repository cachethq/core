<?php

use Cachet\Database\Seeders\DemoMetricSeeder;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

it('records live demo observations into the current bucket', function () {
    Carbon::setTestNow('2026-08-24 12:01:00');
    Event::fake([MetricPointCreated::class]);

    $metric = Metric::factory()->create(['name' => DemoMetricSeeder::METRIC_NAME]);

    $this->seed(DemoMetricSeeder::class);
    $this->seed(DemoMetricSeeder::class);

    expect($metric->metricPoints()->count())->toBe(1)
        ->and($metric->metricPoints()->sole()->counter)->toBe(2)
        ->and($metric->metricPoints()->sole()->sum_value)->toBeGreaterThanOrEqual(2);

    Event::assertNotDispatched(MetricPointCreated::class);
});

it('does nothing when the demo metric has been deleted', function () {
    Metric::factory()->create(['name' => 'Some Other Metric']);

    $this->seed(DemoMetricSeeder::class);

    expect(MetricPoint::query()->count())->toBe(0);
});
