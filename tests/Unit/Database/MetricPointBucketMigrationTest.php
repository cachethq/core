<?php

use Cachet\Models\Metric;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Put the table back the way an install that predates bucketing left it, so
 * the migration has duplicates to collapse.
 */
function seedDuplicateBuckets(Metric $metric): void
{
    Schema::table('metric_points', function (Blueprint $table) {
        $table->dropUnique(['metric_id', 'created_at']);
    });

    DB::table('metric_points')->insert([
        ['metric_id' => $metric->id, 'value' => 1, 'sum_value' => 1, 'counter' => 1, 'created_at' => '2026-07-25 12:00:00', 'updated_at' => '2026-07-25 12:00:00'],
        ['metric_id' => $metric->id, 'value' => 3, 'sum_value' => 6, 'counter' => 2, 'created_at' => '2026-07-25 12:00:00', 'updated_at' => '2026-07-25 12:00:00'],
        ['metric_id' => $metric->id, 'value' => 9, 'sum_value' => 9, 'counter' => 1, 'created_at' => '2026-07-25 12:05:00', 'updated_at' => '2026-07-25 12:05:00'],
        ['metric_id' => $metric->id, 'value' => 4, 'sum_value' => 4, 'counter' => 1, 'created_at' => null, 'updated_at' => null],
        ['metric_id' => $metric->id, 'value' => 5, 'sum_value' => 5, 'counter' => 1, 'created_at' => null, 'updated_at' => null],
    ]);
}

function bucketMigration(): object
{
    return require dirname(__DIR__, 3).'/database/migrations/2026_07_25_000002_add_bucket_index_to_metric_points_table.php';
}

it('collapses duplicate buckets into one point', function () {
    $metric = Metric::factory()->create();

    seedDuplicateBuckets($metric);

    bucketMigration()->up();

    // The duplicated noon bucket keeps the oldest row, carries the totals of
    // both, and reads back the value that arrived most recently.
    $noon = DB::table('metric_points')->where('created_at', '2026-07-25 12:00:00')->get();

    expect($noon)->toHaveCount(1)
        ->and((float) $noon->first()->sum_value)->toBe(7.0)
        ->and((int) $noon->first()->counter)->toBe(3)
        ->and((float) $noon->first()->value)->toBe(3.0);

    expect(DB::table('metric_points')->where('created_at', '2026-07-25 12:05:00')->count())->toBe(1);
});

it('leaves points without a timestamp alone', function () {
    $metric = Metric::factory()->create();

    seedDuplicateBuckets($metric);

    bucketMigration()->up();

    // A unique index treats nulls as distinct, so these were never in the way
    // and merging them would have destroyed data.
    expect(DB::table('metric_points')->whereNull('created_at')->count())->toBe(2);
});

it('leaves a metric only one point per timestamp', function () {
    $metric = Metric::factory()->create();

    seedDuplicateBuckets($metric);

    bucketMigration()->up();

    DB::table('metric_points')->insert([
        'metric_id' => $metric->id,
        'value' => 1,
        'sum_value' => 1,
        'counter' => 1,
        'created_at' => '2026-07-25 12:00:00',
        'updated_at' => '2026-07-25 12:00:00',
    ]);
})->throws(UniqueConstraintViolationException::class);
