<?php

use Cachet\Enums\MetricTypeEnum;
use Cachet\Models\Component;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;

it('has points', function () {
    $metric = Metric::factory()->hasMetricPoints(2)->create();

    expect($metric->metricPoints)->toHaveCount(2);
});

it('can retrieve only the most recent points', function () {
    $metric = Metric::factory()->hasMetricPoints(10)->create();

    expect($metric->recentMetricPoints(5)->get())->toHaveCount(5);
});

it('caps the points the API is willing to embed', function () {
    config()->set('cachet.metrics.max_included_points', 3);

    $metric = Metric::factory()->hasMetricPoints(10)->create();

    expect($metric->includedMetricPoints)->toHaveCount(3);
});

it('calculates counted values', function ($value, $counter, $expected) {
    $metric = Metric::factory()->hasMetricPoints(1, ['value' => $value, 'counter' => $counter])->create();

    expect($metric->metricPoints->first()->calculated_value)->toBe($expected);
})->with([
    // value, counter, expected
    [1, 2, 2.0],
    [2, 5, 10.0],
    [5, 5, 25.0],
]);

it('averages counted values for an average metric', function () {
    $metric = Metric::factory()
        ->hasMetricPoints(1, ['value' => 5, 'counter' => 4])
        ->create(['calc_type' => MetricTypeEnum::average]);

    expect($metric->metricPoints->first()->calculated_value)->toBe(5.0);
});

it('reads the calculation type back without a query per point', function () {
    $metric = Metric::factory()->hasMetricPoints(3)->create();

    expect($metric->metricPoints->every(fn (MetricPoint $point) => $point->relationLoaded('metric')))->toBeTrue();
});

it('can be displayed alongside a component', function () {
    $component = Component::factory()->create();
    $metric = Metric::factory()->create(['component_id' => $component->id]);

    expect($metric->component->is($component))->toBeTrue()
        ->and($component->metrics->first()->is($metric))->toBeTrue();
});
