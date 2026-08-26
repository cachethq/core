<?php

use Cachet\Enums\MetricTypeEnum;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

it('seeds the running total for points created through the model', function () {
    $metric = Metric::factory()->create();

    $point = $metric->metricPoints()->create(['value' => 7, 'counter' => 3]);

    expect($point->sum_value)->toBe(21.0);
});

it('resolves a bucket through its metric\'s calculation type', function (MetricTypeEnum $calcType, float $expected) {
    expect(MetricPoint::resolveValue($calcType, 10.0, 4))->toBe($expected);
})->with([
    [MetricTypeEnum::sum, 10.0],
    [MetricTypeEnum::average, 2.5],
]);

it('resolves an empty bucket without dividing by zero', function () {
    expect(MetricPoint::resolveValue(MetricTypeEnum::average, 0.0, 0))->toBe(0.0);
});

it('rolls points up into hourly totals', function () {
    $metric = Metric::factory()->create(['calc_type' => MetricTypeEnum::average]);

    $metric->metricPoints()->createMany([
        ['value' => 2, 'counter' => 1, 'created_at' => '2026-07-25 12:05:00'],
        ['value' => 4, 'counter' => 1, 'created_at' => '2026-07-25 12:35:00'],
        ['value' => 9, 'counter' => 1, 'created_at' => '2026-07-25 13:05:00'],
    ]);

    $totals = MetricPoint::hourlyTotals([$metric->id], Carbon::parse('2026-07-25 00:00:00'));

    expect($totals[$metric->id])->toHaveCount(2);

    [$noon, $onePm] = $totals[$metric->id];

    expect($noon['at']->toDateTimeString())->toBe('2026-07-25 12:00:00')
        ->and($noon['sum'])->toBe(6.0)
        ->and($noon['counter'])->toBe(2)
        ->and($onePm['sum'])->toBe(9.0)
        ->and($onePm['counter'])->toBe(1);
});

it('keeps hourly totals of different metrics apart', function () {
    $first = Metric::factory()->create();
    $second = Metric::factory()->create();

    $first->metricPoints()->create(['value' => 1, 'created_at' => '2026-07-25 12:05:00']);
    $second->metricPoints()->create(['value' => 5, 'created_at' => '2026-07-25 12:05:00']);

    $totals = MetricPoint::hourlyTotals([$first->id, $second->id], Carbon::parse('2026-07-25 00:00:00'));

    expect($totals[$first->id][0]['sum'])->toBe(1.0)
        ->and($totals[$second->id][0]['sum'])->toBe(5.0);
});

it('prunes points past the retention window', function () {
    config()->set('cachet.metrics.retention_days', 90);

    $metric = Metric::factory()->create();
    $metric->metricPoints()->createMany([
        ['value' => 1, 'created_at' => now()->subDays(91)],
        ['value' => 2, 'created_at' => now()->subDays(89)],
    ]);

    Artisan::call('model:prune', ['--model' => [MetricPoint::class]]);

    expect(MetricPoint::query()->count())->toBe(1)
        ->and(MetricPoint::query()->sole()->value)->toBe(2.0);
});

it('keeps every point when retention is disabled', function (mixed $retention) {
    config()->set('cachet.metrics.retention_days', $retention);

    $metric = Metric::factory()->create();
    $metric->metricPoints()->createMany([
        ['value' => 1, 'created_at' => now()->subYears(5)],
        ['value' => 2, 'created_at' => now()->subDays(1)],
    ]);

    Artisan::call('model:prune', ['--model' => [MetricPoint::class]]);

    expect(MetricPoint::query()->count())->toBe(2);
})->with([null, '', 0]);
