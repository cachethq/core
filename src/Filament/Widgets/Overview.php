<?php

namespace Cachet\Filament\Widgets;

use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Overview extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Incidents', Incident::count())
                ->description(__('Total number of reported incidents.'))
                ->chart(DB::table('incidents')->select(DB::raw('count(*) as total'), 'created_at')->groupBy('created_at')->get()->pluck('total')->toArray())
                ->icon('cachet-incident', IconPosition::Before)
                ->color('info'),
            Stat::make('Total Subscribers', Subscriber::count())
                ->description(__('Total number of reported incidents.'))
                ->chart([2, 3, 5, 4, 6, 8, 10, 12, 14, 16, 18, 20])
                ->icon('cachet-incident', IconPosition::Before)
                ->color('info'),
        ];
    }
}
