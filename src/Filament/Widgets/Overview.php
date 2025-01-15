<?php

namespace Cachet\Filament\Widgets;

use Cachet\Models\Incident;
use Cachet\Models\MetricPoint;
use Cachet\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Overview extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('total_incidents', Incident::count())
                ->label(__('cachet::incident.overview.total_incidents_label'))
                ->description(__('cachet::incident.overview.total_incidents_description'))
                ->chart(DB::table('incidents')->selectRaw('count(*) as total')->groupByRaw('date(created_at)')->pluck('total')->toArray())
                ->icon('cachet-incident')
                ->chartColor('info')
                ->color('gray'),

            Stat::make('metric_points', MetricPoint::count())
                ->label(__('cachet::metric.overview.metric_points_label'))
                ->description(__('cachet::metric.overview.metric_points_description'))
                ->chart(DB::table('metric_points')->selectRaw('count(*) as total')->groupBy('created_at')->pluck('total')->toArray())
                ->icon('cachet-metrics')
                ->chartColor('info')
                ->color('gray'),

            Stat::make('total_subscribers', Subscriber::count())
                ->label(__('cachet::subscriber.overview.total_subscribers_label'))
                ->description(__('cachet::subscriber.overview.total_subscribers_description'))
                ->chart(DB::table('subscribers')->selectRaw('count(*) as total')->groupByRaw('date(created_at)')->pluck('total')->toArray())
                ->icon('cachet-subscribers')
                ->chartColor('info')
                ->color('gray'),
        ];
    }
}
