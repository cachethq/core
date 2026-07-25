<?php

namespace Cachet\Actions\Metric;

use Cachet\Concerns\RecordsMetricObservations;
use Cachet\Data\Metrics\MetricObservation;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Carbon\CarbonInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Cachet's default recorder: observations accumulate into fixed-width buckets.
 *
 * A metric point is a bucket, not a sample. Each observation adds its value to
 * the bucket's running total and increments its counter, so nothing that was
 * submitted is discarded, and the metric's calculation type decides how the
 * bucket reads back — a sum of the observations, or their average.
 *
 * The accumulation is a single upsert statement, so concurrent writers cannot
 * lose an observation or leave duplicate rows behind. This means the write
 * never loads a model, and so metric points recorded through this action
 * dispatch no model events: MetricPointCreated, and the webhook behind
 * it, no longer fire per observation.
 *
 * The bucket width is frozen at write time. Editing a metric's threshold
 * changes how later observations are bucketed and leaves the buckets
 * already written alone.
 */
class RecordMetricObservations implements RecordsMetricObservations
{
    /**
     * The bucket width, in seconds, for a metric with no threshold set.
     */
    private const DEFAULT_BUCKET_SECONDS = 30;

    /**
     * Record a single observation against a metric.
     */
    public function record(Metric $metric, MetricObservation $observation): MetricPoint
    {
        return $this->recordMany($metric, [$observation])->firstOrFail();
    }

    /**
     * Record a batch of observations against a metric.
     *
     * Observations landing in the same bucket are combined before they are
     * written, so a batch costs one statement per bucket it touches.
     *
     * @param  iterable<int, MetricObservation>  $observations
     * @return Collection<int, MetricPoint>
     */
    public function recordMany(Metric $metric, iterable $observations): Collection
    {
        $buckets = $this->bucket($metric, $observations);

        if ($buckets->isEmpty()) {
            return new Collection;
        }

        foreach ($buckets as $bucket) {
            $this->accumulate($metric, $bucket);
        }

        return $this->points($metric, $buckets->keys()->all());
    }

    /**
     * Combine the observations into the buckets they belong to, keyed by the
     * bucket's timestamp. The last observation to land in a bucket sets its
     * "value", which is the most recent reading rather than a total.
     *
     * @param  iterable<int, MetricObservation>  $observations
     * @return Collection<string, array{at: CarbonInterface, value: float, sum: float, counter: int}>
     */
    private function bucket(Metric $metric, iterable $observations): Collection
    {
        $width = $this->bucketSeconds($metric);

        /** @var Collection<string, array{at: CarbonInterface, value: float, sum: float, counter: int}> $buckets */
        $buckets = new Collection;

        foreach ($observations as $observation) {
            $at = $this->bucketFor($observation->timestamp(), $width);
            $key = $at->toDateTimeString();

            $buckets->put($key, [
                'at' => $at,
                'value' => $observation->value,
                'sum' => ($buckets->get($key)['sum'] ?? 0.0) + $observation->value,
                'counter' => ($buckets->get($key)['counter'] ?? 0) + 1,
            ]);
        }

        return $buckets;
    }

    /**
     * Floor a timestamp onto the bucket boundary it falls within.
     */
    private function bucketFor(CarbonInterface $timestamp, int $width): CarbonInterface
    {
        return Carbon::createFromTimestamp(intdiv($timestamp->getTimestamp(), $width) * $width);
    }

    /**
     * The bucket width, in seconds, for a metric.
     */
    private function bucketSeconds(Metric $metric): int
    {
        $threshold = (int) $metric->threshold;

        return $threshold > 0 ? $threshold * 60 : self::DEFAULT_BUCKET_SECONDS;
    }

    /**
     * Add a bucket's observations to the metric's point for that bucket,
     * creating the point when this is the first observation to land in it.
     *
     * @param  array{at: CarbonInterface, value: float, sum: float, counter: int}  $bucket
     */
    private function accumulate(Metric $metric, array $bucket): void
    {
        $connection = $metric->getConnection();

        if (($statement = $this->upsertStatement($connection)) === null) {
            $this->accumulateWithoutUpsert($connection, $metric, $bucket);

            return;
        }

        $connection->statement($statement, [
            $metric->getKey(),
            $bucket['value'],
            $bucket['sum'],
            $bucket['counter'],
            $bucket['at'],
            Carbon::now(),
        ]);
    }

    /**
     * Build the accumulating upsert for the connection's driver.
     *
     * Laravel's own upsert() overwrites the conflicting row, so the totals
     * have to be added in driver specific SQL. MySQL's values() form is
     * used over the newer row alias so MariaDB is covered too.
     */
    private function upsertStatement(Connection $connection): ?string
    {
        $grammar = $connection->getQueryGrammar();
        $table = $grammar->wrapTable((new MetricPoint)->getTable());

        $columns = [];

        foreach (['metric_id', 'value', 'sum_value', 'counter', 'created_at', 'updated_at'] as $column) {
            $columns[$column] = $grammar->wrap($column);
        }

        $insert = sprintf(
            'insert into %s (%s) values (?, ?, ?, ?, ?, ?)',
            $table,
            implode(', ', $columns),
        );

        return match ($connection->getDriverName()) {
            'mysql', 'mariadb' => $insert.sprintf(
                ' on duplicate key update %s = %s + values(%s), %s = %s + values(%s), %s = values(%s), %s = values(%s)',
                $columns['sum_value'], $columns['sum_value'], $columns['sum_value'],
                $columns['counter'], $columns['counter'], $columns['counter'],
                $columns['value'], $columns['value'],
                $columns['updated_at'], $columns['updated_at'],
            ),
            'pgsql', 'sqlite' => $insert.sprintf(
                ' on conflict (%s, %s) do update set %s = %s.%s + excluded.%s, %s = %s.%s + excluded.%s, %s = excluded.%s, %s = excluded.%s',
                $columns['metric_id'], $columns['created_at'],
                $columns['sum_value'], $table, $columns['sum_value'], $columns['sum_value'],
                $columns['counter'], $table, $columns['counter'], $columns['counter'],
                $columns['value'], $columns['value'],
                $columns['updated_at'], $columns['updated_at'],
            ),
            default => null,
        };
    }

    /**
     * Accumulate on a driver with no accumulating upsert of its own.
     *
     * The update is still a single atomic statement; only the first
     * observation in a bucket costs a second round trip, and a writer
     * that loses the race to insert simply accumulates instead.
     *
     * @param  array{at: CarbonInterface, value: float, sum: float, counter: int}  $bucket
     */
    private function accumulateWithoutUpsert(Connection $connection, Metric $metric, array $bucket): void
    {
        if ($this->increment($connection, $metric, $bucket) > 0) {
            return;
        }

        try {
            $this->table($connection)->insert([
                'metric_id' => $metric->getKey(),
                'value' => $bucket['value'],
                'sum_value' => $bucket['sum'],
                'counter' => $bucket['counter'],
                'created_at' => $bucket['at'],
                'updated_at' => Carbon::now(),
            ]);
        } catch (UniqueConstraintViolationException) {
            $this->increment($connection, $metric, $bucket);
        }
    }

    /**
     * Add a bucket's totals to an existing point, returning the rows affected.
     *
     * @param  array{at: CarbonInterface, value: float, sum: float, counter: int}  $bucket
     */
    private function increment(Connection $connection, Metric $metric, array $bucket): int
    {
        return $this->table($connection)
            ->where('metric_id', $metric->getKey())
            ->where('created_at', $bucket['at'])
            ->incrementEach(
                ['sum_value' => $bucket['sum'], 'counter' => $bucket['counter']],
                ['value' => $bucket['value'], 'updated_at' => Carbon::now()],
            );
    }

    /**
     * A query against the metric points table, without the model's events.
     */
    private function table(Connection $connection): QueryBuilder
    {
        return $connection->table((new MetricPoint)->getTable());
    }

    /**
     * Read the buckets back, with their metric already attached so callers
     * can resolve each point's value without a query per point.
     *
     * @param  list<string>  $buckets
     * @return Collection<int, MetricPoint>
     */
    private function points(Metric $metric, array $buckets): Collection
    {
        return MetricPoint::query()
            ->where('metric_id', $metric->getKey())
            ->whereIn('created_at', $buckets)
            ->orderBy('created_at')
            ->get()
            ->each(fn (MetricPoint $point) => $point->setRelation('metric', $metric));
    }
}
