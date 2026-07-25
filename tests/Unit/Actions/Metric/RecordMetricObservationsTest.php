<?php

use Cachet\Actions\Metric\RecordMetricObservations;
use Cachet\Concerns\RecordsMetricObservations;
use Cachet\Data\Metrics\MetricObservation;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Carbon\Carbon;
use Illuminate\Database\MariaDbConnection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Collection;

it('binds the core recorder as the default implementation', function () {
    expect(app(RecordsMetricObservations::class))->toBeInstanceOf(RecordMetricObservations::class);
});

it('can be rebound by a monitoring package', function () {
    $fake = new class implements RecordsMetricObservations
    {
        public function record(Metric $metric, MetricObservation $observation): MetricPoint
        {
            return $this->recordMany($metric, [$observation])->firstOrFail();
        }

        public function recordMany(Metric $metric, iterable $observations): Collection
        {
            return new Collection([new MetricPoint(['value' => 99])]);
        }
    };

    app()->instance(RecordsMetricObservations::class, $fake);

    $point = app(RecordsMetricObservations::class)->record(
        Metric::factory()->create(),
        new MetricObservation(value: 1),
    );

    expect($point->value)->toBe(99.0);
    $this->assertDatabaseCount('metric_points', 0);
});

it('folds a batch into one point per bucket', function () {
    $metric = Metric::factory()->create(['threshold' => 5]);

    $points = app(RecordsMetricObservations::class)->recordMany($metric, [
        new MetricObservation(value: 1, recordedAt: Carbon::parse('2026-07-25 12:01:00')),
        new MetricObservation(value: 2, recordedAt: Carbon::parse('2026-07-25 12:03:00')),
        new MetricObservation(value: 4, recordedAt: Carbon::parse('2026-07-25 12:04:59')),
        new MetricObservation(value: 8, recordedAt: Carbon::parse('2026-07-25 12:05:00')),
    ]);

    expect($points)->toHaveCount(2);
    $this->assertDatabaseCount('metric_points', 2);

    expect($points->first()->created_at->toDateTimeString())->toBe('2026-07-25 12:00:00')
        ->and($points->first()->sum_value)->toBe(7.0)
        ->and($points->first()->counter)->toBe(3);

    expect($points->last()->created_at->toDateTimeString())->toBe('2026-07-25 12:05:00')
        ->and($points->last()->sum_value)->toBe(8.0)
        ->and($points->last()->counter)->toBe(1);
});

it('accumulates a batch onto buckets that already exist', function () {
    $metric = Metric::factory()->create(['threshold' => 5]);
    $recorder = app(RecordsMetricObservations::class);

    $recorder->record($metric, new MetricObservation(value: 3, recordedAt: Carbon::parse('2026-07-25 12:02:00')));
    $recorder->recordMany($metric, [
        new MetricObservation(value: 5, recordedAt: Carbon::parse('2026-07-25 12:03:00')),
        new MetricObservation(value: 7, recordedAt: Carbon::parse('2026-07-25 12:04:00')),
    ]);

    $this->assertDatabaseCount('metric_points', 1);
    $point = MetricPoint::query()->sole();

    expect($point->sum_value)->toBe(15.0)
        ->and($point->counter)->toBe(3)
        ->and($point->value)->toBe(7.0);
});

it('returns an empty collection for an empty batch', function () {
    $points = app(RecordsMetricObservations::class)->recordMany(Metric::factory()->create(), []);

    expect($points)->toBeEmpty();
    $this->assertDatabaseCount('metric_points', 0);
});

it('freezes the bucket width at write time', function () {
    $metric = Metric::factory()->create(['threshold' => 60]);
    $recorder = app(RecordsMetricObservations::class);

    $recorder->record($metric, new MetricObservation(value: 1, recordedAt: Carbon::parse('2026-07-25 12:30:00')));

    // Narrowing the threshold buckets later observations differently and
    // leaves everything already written alone.
    $metric->update(['threshold' => 5]);

    $recorder->record($metric, new MetricObservation(value: 2, recordedAt: Carbon::parse('2026-07-25 12:47:00')));

    expect(MetricPoint::query()->orderBy('created_at')->pluck('created_at')->map->toDateTimeString()->all())
        ->toBe(['2026-07-25 12:00:00', '2026-07-25 12:45:00']);
});

it('buckets to thirty seconds when a metric has no threshold', function () {
    $metric = Metric::factory()->create(['threshold' => 0]);

    $point = app(RecordsMetricObservations::class)->record(
        $metric,
        new MetricObservation(value: 1, recordedAt: Carbon::parse('2026-07-25 12:00:44')),
    );

    expect($point->created_at->toDateTimeString())->toBe('2026-07-25 12:00:30');
});

it('defaults an observation without a timestamp to now', function () {
    Carbon::setTestNow('2026-07-25 12:07:00');

    $metric = Metric::factory()->create(['threshold' => 5]);

    $point = app(RecordsMetricObservations::class)->record($metric, new MetricObservation(value: 1));

    expect($point->created_at->toDateTimeString())->toBe('2026-07-25 12:05:00');
});

it('attaches the metric to the points it returns', function () {
    $metric = Metric::factory()->create();

    $point = app(RecordsMetricObservations::class)->record($metric, new MetricObservation(value: 1));

    expect($point->relationLoaded('metric'))->toBeTrue()
        ->and($point->metric->is($metric))->toBeTrue();
});

it('builds an accumulating upsert for each driver', function (string $connection, string $driver, ?string $expected) {
    $connection = new $connection(fn () => null, 'testing', '', ['driver' => $driver]);

    // Only SQLite runs in the test suite, so the statement the other drivers
    // would be sent is pinned here rather than left unverified.
    $statement = (new ReflectionMethod(RecordMetricObservations::class, 'upsertStatement'))
        ->invoke(new RecordMetricObservations, $connection);

    expect($statement)->toBe($expected);
})->with([
    [
        MySqlConnection::class,
        'mysql',
        'insert into `metric_points` (`metric_id`, `value`, `sum_value`, `counter`, `created_at`, `updated_at`) values (?, ?, ?, ?, ?, ?)'
        .' on duplicate key update `sum_value` = `sum_value` + values(`sum_value`), `counter` = `counter` + values(`counter`),'
        .' `value` = values(`value`), `updated_at` = values(`updated_at`)',
    ],
    [
        MariaDbConnection::class,
        'mariadb',
        'insert into `metric_points` (`metric_id`, `value`, `sum_value`, `counter`, `created_at`, `updated_at`) values (?, ?, ?, ?, ?, ?)'
        .' on duplicate key update `sum_value` = `sum_value` + values(`sum_value`), `counter` = `counter` + values(`counter`),'
        .' `value` = values(`value`), `updated_at` = values(`updated_at`)',
    ],
    [
        PostgresConnection::class,
        'pgsql',
        'insert into "metric_points" ("metric_id", "value", "sum_value", "counter", "created_at", "updated_at") values (?, ?, ?, ?, ?, ?)'
        .' on conflict ("metric_id", "created_at") do update set "sum_value" = "metric_points"."sum_value" + excluded."sum_value",'
        .' "counter" = "metric_points"."counter" + excluded."counter", "value" = excluded."value", "updated_at" = excluded."updated_at"',
    ],
    [
        // No accumulating upsert: the recorder falls back to an atomic
        // increment, and an insert only when the bucket does not exist.
        SqlServerConnection::class,
        'sqlsrv',
        null,
    ],
]);
