<?php

namespace Cachet\View\Components;

use Cachet\Models\Metric;
use Cachet\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

    public function __construct(protected AppSettings $appSettings)
    {
        //
    }

    public function render(): View
    {
        $cacheKey = 'cachet::metrics.'.(auth()->check() ? 'users' : 'guests');

        $cached = Cache::remember($cacheKey, self::CACHE_TTL, fn (): Collection => $this->prepareMetrics());

        return view('cachet::components.metrics', [
            'metrics' => $this->validMetrics($cached, $cacheKey),
        ]);
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
     * Build the metrics collection with each point cast to Chart.js format.
     */
    private function prepareMetrics(): Collection
    {
        $metrics = $this->metrics(Carbon::now()->subDays(30));

        $metrics->each(function ($metric) {
            $metric->metricPoints->transform(fn ($point) => [
                'x' => $point->created_at->utc(),
                'y' => $point->value,
            ]);
        });

        return $metrics;
    }

    /**
     * Fetch the available metrics and their points within the chart window.
     */
    private function metrics(Carbon $startDate): Collection
    {
        return Metric::query()
            ->visible(auth()->check())
            ->with([
                'metricPoints' => fn ($query) => $query->where('created_at', '>=', $startDate)->orderBy('created_at'),
            ])
            ->where('display_chart', true)
            ->where(fn (Builder $query) => $query->where('show_when_empty', true)->orWhereHas('metricPoints', fn (Builder $query) => $query->where('created_at', '>=', $startDate)))
            ->orderBy('places')
            ->get();
    }
}
