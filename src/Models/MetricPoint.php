<?php

namespace Cachet\Models;

use Cachet\Database\Factories\MetricPointFactory;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Events\Metrics\MetricPointCreated;
use Cachet\Events\Metrics\MetricPointDeleted;
use DateTime;
use DateTimeInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A bucket of observations recorded against a metric.
 *
 * "value" is the most recent observation to land in the bucket, "sum_value"
 * the total of every observation in it, and "counter" how many there were.
 * The metric's calculation type decides which of those it reads back as.
 *
 * @property int $id
 * @property int $metric_id
 * @property float $value
 * @property float $sum_value
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int $counter
 * @property Metric $metric
 * @property-read float $calculated_value
 *
 * @method static MetricPointFactory factory($count = null, $state = [])
 */
class MetricPoint extends Model
{
    /** @use HasFactory<MetricPointFactory> */
    use HasFactory;

    use MassPrunable;

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'float',
        'sum_value' => 'float',
    ];

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => MetricPointCreated::class,
        'deleted' => MetricPointDeleted::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'value',
        'sum_value',
        'counter',
        'created_at',
    ];

    /**
     * Combine a bucket's totals into the value it displays as.
     */
    public static function resolveValue(MetricTypeEnum $calcType, float $sum, int $counter): float
    {
        return match ($calcType) {
            MetricTypeEnum::sum => $sum,
            MetricTypeEnum::average => $counter > 0 ? $sum / $counter : $sum,
        };
    }

    /**
     * Roll the given metrics' points up into hourly totals.
     *
     * The wider chart windows do not need every bucket, and reading 30 days
     * of raw points to draw a month of chart is what made the status page
     * inline megabytes of JSON. Returns null when the connection's driver
     * has no hour expression, leaving the caller to read raw points.
     *
     * @param  array<int, int>  $metricIds
     * @return ?array<int, list<array{at: Carbon, sum: float, counter: int}>>
     */
    public static function hourlyTotals(array $metricIds, DateTimeInterface $since): ?array
    {
        if ($metricIds === []) {
            return [];
        }

        $instance = new self;
        $connection = $instance->getConnection();

        if (($hour = self::hourExpression($connection)) === null) {
            return null;
        }

        $grammar = $connection->getQueryGrammar();

        $rows = $connection->table($instance->getTable())
            ->selectRaw(sprintf(
                '%s as metric_id, %s as bucket, sum(%s) as sum_value, sum(%s) as counter',
                $grammar->wrap('metric_id'),
                $hour,
                $grammar->wrap('sum_value'),
                $grammar->wrap('counter'),
            ))
            ->whereIn('metric_id', $metricIds)
            ->where('created_at', '>=', $since)
            ->groupByRaw(sprintf('%s, %s', $grammar->wrap('metric_id'), $hour))
            ->orderByRaw($hour)
            ->get();

        $totals = [];

        foreach ($rows as $row) {
            $totals[(int) $row->metric_id][] = [
                'at' => Carbon::parse($row->bucket),
                'sum' => (float) $row->sum_value,
                'counter' => (int) $row->counter,
            ];
        }

        return $totals;
    }

    /**
     * The bucket's value, resolved through its metric's calculation type.
     */
    public function calculatedValue(): Attribute
    {
        return Attribute::make(
            get: fn (): float => self::resolveValue(
                $this->calcType(),
                (float) $this->sum_value,
                (int) $this->counter,
            )
        );
    }

    /**
     * The calculation type of the metric this point belongs to.
     *
     * A point orphaned from its metric reads back as a sum, which is what
     * every point was before the calculation type meant anything.
     */
    public function calcType(): MetricTypeEnum
    {
        $metric = $this->getRelationValue('metric');

        return $metric instanceof Metric ? $metric->calc_type : MetricTypeEnum::sum;
    }

    /**
     * Override the created_at column to round the value into a 30-second interval.
     */
    public function createdAt(): Attribute
    {
        return Attribute::make(
            set: function ($createdAt) {
                if (! $createdAt) {
                    return;
                }

                if (! $createdAt instanceof DateTime) {
                    $createdAt = Carbon::parse($createdAt);
                }

                $timestamp = $createdAt->getTimestamp();
                $timestamp = 30 * round($timestamp / 30);

                return Carbon::createFromTimestamp($timestamp, config('app.timezone'));
            }
        );
    }

    /**
     * Get the metric the point belongs to.
     *
     * @return BelongsTo<Metric, $this>
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class);
    }

    /**
     * Determine if the metric point was created within the threshold.
     */
    public function withinThreshold(int $threshold, string|int|DateTime|null $timestamp = null): bool
    {
        if (blank($timestamp)) {
            $now = now();
        }

        $now ??= Carbon::parse($timestamp);

        return $this->created_at->startOfMinute()->diffInMinutes($now->startOfMinute(), true) < $threshold;
    }

    /**
     * Get the prunable model query.
     *
     * A retention of null — or anything that is not a positive number of
     * days — keeps every point forever, and lets the table grow without
     * bound. Nothing is ever pruned by accident.
     *
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        $days = config('cachet.metrics.retention_days', 90);

        if (! is_numeric($days) || (int) $days <= 0) {
            return static::query()->whereRaw('1 = 0');
        }

        return static::query()->where('created_at', '<', now()->subDays((int) $days));
    }

    /**
     * Seed the running total for points created through the model, so a point
     * written outside the recorder still reads back correctly.
     */
    protected static function booted(): void
    {
        static::creating(function (MetricPoint $point): void {
            if ($point->getAttribute('sum_value') === null) {
                $point->setAttribute(
                    'sum_value',
                    (float) $point->value * max(1, (int) ($point->counter ?? 1)),
                );
            }
        });
    }

    /**
     * Truncate a point's timestamp to the hour it falls in.
     */
    private static function hourExpression(Connection $connection): ?string
    {
        $column = $connection->getQueryGrammar()->wrap('created_at');

        return match ($connection->getDriverName()) {
            'mysql', 'mariadb' => "date_format({$column}, '%Y-%m-%d %H:00:00')",
            'pgsql' => "to_char({$column}, 'YYYY-MM-DD HH24:00:00')",
            'sqlite' => "strftime('%Y-%m-%d %H:00:00', {$column})",
            'sqlsrv' => "format({$column}, 'yyyy-MM-dd HH:00:00')",
            default => null,
        };
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return MetricPointFactory::new();
    }
}
