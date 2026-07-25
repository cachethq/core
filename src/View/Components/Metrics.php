<?php

namespace Cachet\View\Components;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Cachet\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Metrics extends Component
{
    /**
     * How long the metrics are cached for, in seconds.
     */
    private const CACHE_TTL = 30;

    /**
     * How far back the raw buckets behind the hour and day windows reach.
     */
    private const RAW_WINDOW_HOURS = 24;

    /**
     * How far back the hourly totals behind the week and month windows reach.
     */
    private const HOURLY_WINDOW_DAYS = 30;

    public function __construct(protected AppSettings $appSettings)
    {
        //
    }

    public function render(): View
    {
        $cacheKey = $this->cacheKey();

        $cached = Cache::remember($cacheKey, self::CACHE_TTL, fn (): Collection => $this->prepareMetrics());

        return view('cachet::components.metrics', [
            'metrics' => $this->validMetrics($cached, $cacheKey),
        ]);
    }

    /**
     * The cache key the prepared metrics are stored under.
     *
     * Cachet's cache keys are tenant-agnostic: they identify the audience a
     * value was built for, never the installation it belongs to. Anything
     * running more than one Cachet against a shared cache store has to
     * separate them with the cache store's own prefix.
     */
    private function cacheKey(): string
    {
        return 'cachet::metrics.'.(auth()->check() ? 'users' : 'guests');
    }

    /**
     * Return the cached metrics, or rebuild them from the database when the
     * cached value is not a collection of metrics. A stale entry left behind
     * after switching cache stores can deserialize to something else entirely,
     * which would otherwise fatal the view reading properties off each item.
     */
    private function validMetrics(mixed $cached, string $cacheKey): Collection
    {
        if ($cached instanceof Collection && $cached->every(fn (mixed $metric): bool => $metric instanceof Metric)) {
            return $cached;
        }

        Cache::forget($cacheKey);

        return $this->prepareMetrics();
    }

    /**
     * Build the metrics collection, with the series each chart draws attached.
     *
     * The hour and day windows are drawn from raw buckets, and the week and
     * month windows from hourly totals rolled up in the database. A month of
     * raw buckets was previously inlined into the page to draw all four.
     */
    private function prepareMetrics(): Collection
    {
        $rawSince = Carbon::now()->subHours(self::RAW_WINDOW_HOURS);
        $hourlySince = Carbon::now()->subDays(self::HOURLY_WINDOW_DAYS);

        $metrics = $this->metrics($hourlySince);

        if ($metrics->isEmpty()) {
            return $metrics;
        }

        $metricIds = $metrics->modelKeys();

        $raw = $this->rawTotals($metricIds, $rawSince);
        $hourly = $this->hourlyTotals($metricIds, $hourlySince)
            ?? $this->rawTotals($metricIds, $hourlySince);

        return $metrics->each(fn (Metric $metric) => $metric->setAttribute('chart_points', [
            'raw' => $this->chartPoints($metric, $raw->get($metric->getKey(), [])),
            'hourly' => $this->chartPoints($metric, $hourly->get($metric->getKey(), [])),
        ]));
    }

    /**
     * Fetch the metrics with a chart to draw.
     *
     * @return EloquentCollection<int, Metric>
     */
    private function metrics(Carbon $startDate): EloquentCollection
    {
        return Metric::query()
            ->visible(auth()->check())
            ->with('component')
            ->where('display_chart', true)
            ->where(fn (Builder $query) => $query->where('show_when_empty', true)->orWhereHas('metricPoints', fn (Builder $query) => $query->where('created_at', '>=', $startDate)))
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Read the raw buckets for the given metrics, keyed by metric.
     *
     * @param  list<int>  $metricIds
     * @return Collection<int|string, list<array{at: Carbon, sum: float, counter: int}>>
     */
    private function rawTotals(array $metricIds, Carbon $since): Collection
    {
        return MetricPoint::query()
            ->whereIn('metric_id', $metricIds)
            ->where('created_at', '>=', $since)
            ->orderBy('created_at')
            ->get(['metric_id', 'created_at', 'sum_value', 'counter'])
            ->groupBy(fn (MetricPoint $point): int => (int) $point->metric_id)
            ->map(function (EloquentCollection $points): array {
                $totals = [];

                foreach ($points as $point) {
                    if (! ($at = $point->created_at) instanceof Carbon) {
                        continue;
                    }

                    $totals[] = [
                        'at' => $at,
                        'sum' => (float) $point->sum_value,
                        'counter' => (int) $point->counter,
                    ];
                }

                return $totals;
            });
    }

    /**
     * Roll the given metrics' buckets up into hourly totals, keyed by metric.
     *
     * Returns null when the database cannot do the rollup, leaving the caller
     * to fall back to raw buckets.
     *
     * @param  list<int>  $metricIds
     * @return ?Collection<int, list<array{at: Carbon, sum: float, counter: int}>>
     */
    private function hourlyTotals(array $metricIds, Carbon $since): ?Collection
    {
        return MetricPoint::hourlyTotals($metricIds, $since)
            ?->map(function (Collection $rows): array {
                $totals = [];

                foreach ($rows as $row) {
                    $totals[] = [
                        'at' => Carbon::parse($row->bucket),
                        'sum' => (float) $row->sum_value,
                        'counter' => (int) $row->counter,
                    ];
                }

                return $totals;
            });
    }

    /**
     * Cast a metric's totals into the series Chart.js draws, resolving each
     * bucket through the metric's calculation type.
     *
     * @param  list<array{at: Carbon, sum: float, counter: int}>  $totals
     * @return list<array{x: string, y: float}>
     */
    private function chartPoints(Metric $metric, array $totals): array
    {
        $places = $metric->places ?? 2;

        return array_map(fn (array $total): array => [
            'x' => $total['at']->utc()->toIso8601String(),
            'y' => round(
                MetricPoint::resolveValue(
                    $metric->calc_type ?? MetricTypeEnum::sum,
                    $total['sum'],
                    $total['counter'],
                ),
                $places,
            ),
        ], $totals);
    }
}
