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
            Stat::make(__('Total Incidents'), Incident::count())
                ->description(__('Total number of reported incidents.'))
                ->chart(DB::table('incidents')->selectRaw('count(*) as total')->groupByRaw('date(created_at)')->pluck('total')->toArray())
                ->icon('cachet-incident')
                ->chartColor('info')
                ->color('gray'),

            Stat::make(__('Metric Points'), MetricPoint::count())
                ->description(__('Recent metric points.'))
                ->chart(DB::table('metric_points')->selectRaw('count(*) as total')->groupBy('created_at')->pluck('total')->toArray())
                ->icon('cachet-metrics')
                ->chartColor('info')
                ->color('gray'),

            Stat::make(__('Total Subscribers'), Subscriber::count())
                ->description(__('Total number of subscribers.'))
                ->chart(DB::table('subscribers')->selectRaw('count(*) as total')->groupByRaw('date(created_at)')->pluck('total')->toArray())
                ->icon('cachet-subscribers')
                ->chartColor('info')
                ->color('gray'),
        ];
    }
}
