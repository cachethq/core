<?php

use Cachet\Database\Seeders\DemoMetricSeeder;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;

it('pushes a metric point onto the demo metric', function () {
    $metric = Metric::factory()->create(['name' => DemoMetricSeeder::METRIC_NAME]);

    $this->seed(DemoMetricSeeder::class);

    expect($metric->metricPoints()->count())->toBe(1)
        ->and($metric->metricPoints()->first()->value)->toBeGreaterThanOrEqual(1);
});

it('does nothing when the demo metric has been deleted', function () {
    Metric::factory()->create(['name' => 'Some Other Metric']);

    $this->seed(DemoMetricSeeder::class);

    expect(MetricPoint::query()->count())->toBe(0);
});
