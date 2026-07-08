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

        $metrics = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $startDate = Carbon::now()->subDays(30);

            $metrics = $this->metrics($startDate);

            // Convert each metric point to Chart.js format (x, y)
            $metrics->each(function ($metric) {
                $metric->metricPoints->transform(fn ($point) => [
                    'x' => $point->created_at->utc(),
                    'y' => $point->value,
                ]);
            });

            return $metrics;
        });

        return view('cachet::components.metrics', [
            'metrics' => $metrics,
        ]);
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
