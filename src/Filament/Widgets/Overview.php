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
            Stat::make('Total Incidents', Incident::count())
                ->description(__('Total number of reported incidents.'))
                ->chart(DB::table('incidents')->select(DB::raw('count(*) as total'), 'created_at')->groupBy('created_at')->get()->pluck('total')->toArray())
                ->icon('cachet-incident')
                ->chartColor('primary')
                ->color('gray'),

            Stat::make('Metric Points', MetricPoint::count())
                ->description(__('Total number of metric points.'))
                ->chart(DB::table('metric_points')->select(DB::raw('count(*) as total'), 'created_at')->groupBy('created_at')->get()->pluck('total')->toArray())
                ->icon('cachet-metrics')
                ->chartColor('info')
                ->color('gray'),

            Stat::make('Total Subscribers', Subscriber::count())
                ->description(__('Total number of subscribers.'))
                ->chart(DB::table('subscribers')->select(DB::raw('count(*) as total'), 'created_at')->groupBy('created_at')->get()->pluck('total')->toArray())
                ->icon('cachet-subscribers')
                ->chartColor('info')
                ->color('gray'),
        ];
    }
}
