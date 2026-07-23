<?php

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
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
