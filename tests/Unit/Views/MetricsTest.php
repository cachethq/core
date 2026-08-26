<?php

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Cachet\View\Components\Metrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;

it('rebuilds the metrics when the cache holds a corrupt value', function () {
    $metric = Metric::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);
    MetricPoint::factory()->count(2)->create(['metric_id' => $metric->id]);

    // A stale entry left by another cache store deserializes to strings, not
    // metrics — the view would fatal reading ->id on each.
    Cache::put('cachet::metrics.guests', collect(['not-a-metric']), 30);

    $html = Blade::render('<x-cachet::metrics />');

    expect($html)->toContain('chart_'.$metric->id);
});

it('renders nothing when the metric component is given a non-metric', function () {
    $html = Blade::render('<x-cachet::metric :metric="$metric" />', ['metric' => 'not-a-metric']);

    expect(trim($html))->toBe('');
});

it('caches a valid metrics collection', function () {
    $metric = Metric::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);

    Blade::render('<x-cachet::metrics />');

    expect(Cache::get('cachet::metrics.guests'))
        ->toBeInstanceOf(Collection::class)
        ->first()->toBeInstanceOf(Metric::class);
});

it('orders the charts by the metric order, not its decimal places', function () {
    Metric::factory()->create([
        'name' => 'second',
        'order' => 2,
        'places' => 0,
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);
    Metric::factory()->create([
        'name' => 'first',
        'order' => 1,
        'places' => 4,
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);

    $html = Blade::render('<x-cachet::metrics />');

    expect(strpos($html, 'first'))->toBeLessThan(strpos($html, 'second'));
});

it('draws the wider windows from hourly totals', function () {
    $metric = Metric::factory()->create([
        'calc_type' => MetricTypeEnum::sum,
        'places' => 2,
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);

    // Two buckets inside the same hour, well outside the raw window.
    $metric->metricPoints()->createMany([
        ['value' => 2, 'created_at' => now()->subDays(3)->startOfHour()],
        ['value' => 3, 'created_at' => now()->subDays(3)->startOfHour()->addMinutes(30)],
    ]);

    $chartPoints = app(Metrics::class)
        ->render()
        ->getData()['metrics']
        ->first()
        ->chart_points;

    expect($chartPoints['raw'])->toBeEmpty()
        ->and($chartPoints['hourly'])->toHaveCount(1)
        ->and($chartPoints['hourly'][0]['y'])->toBe(5.0);
});

it('resolves the chart series through the metric calculation type', function () {
    $metric = Metric::factory()->create([
        'calc_type' => MetricTypeEnum::average,
        'places' => 2,
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);

    $metric->metricPoints()->create([
        'value' => 4,
        'counter' => 4,
        'created_at' => now()->subMinutes(10),
    ]);

    $chartPoints = app(Metrics::class)
        ->render()
        ->getData()['metrics']
        ->first()
        ->chart_points;

    expect($chartPoints['raw'])->toHaveCount(1)
        ->and($chartPoints['raw'][0]['y'])->toBe(4.0);
});

it('labels a chart with the component it is displayed alongside', function () {
    $component = Component::factory()->create(['name' => 'API Gateway']);

    Metric::factory()->create([
        'component_id' => $component->id,
        'visible' => ResourceVisibilityEnum::guest,
        'display_chart' => true,
        'show_when_empty' => true,
    ]);

    $html = Blade::render('<x-cachet::metrics />');

    expect($html)->toContain('API Gateway');
});
