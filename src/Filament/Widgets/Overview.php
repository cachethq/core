<?php

namespace Cachet\Filament\Widgets;

use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class Overview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $openIncidents = Incident::query()->unresolved()->count();
        $components = app(Status::class)->components();
        $totalComponents = (int) $components->total;
        $operationalComponents = (int) $components->operational;
        $allOperational = $totalComponents === $operationalComponents;

        return [
            Stat::make('open_incidents', $openIncidents)
                ->label(__('cachet::incident.overview.open_incidents_label'))
                ->description(__('cachet::incident.overview.open_incidents_description'))
                ->chart($this->dailyCounts('incidents'))
                ->icon('cachet-incident')
                ->chartColor($openIncidents > 0 ? 'danger' : 'success')
                ->color($openIncidents > 0 ? 'danger' : 'success'),

            Stat::make('operational_components', "{$operationalComponents} / {$totalComponents}")
                ->label(__('cachet::component.overview.operational_components_label'))
                ->description(__('cachet::component.overview.operational_components_description'))
                ->icon('cachet-components')
                ->color($allOperational ? 'success' : 'warning'),

            Stat::make('total_subscribers', Subscriber::count())
                ->label(__('cachet::subscriber.overview.total_subscribers_label'))
                ->description(__('cachet::subscriber.overview.verified_subscribers_description', [
                    'count' => Subscriber::query()->whereNotNull('email_verified_at')->count(),
                ]))
                ->chart($this->dailyCounts('subscribers'))
                ->icon('cachet-subscribers')
                ->chartColor('info')
                ->color('gray'),
        ];
    }

    /**
     * Get the number of records created per day over the last 30 days.
     *
     * @return array<int, int>
     */
    protected function dailyCounts(string $table): array
    {
        return DB::table($table)
            ->selectRaw('count(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupByRaw('date(created_at)')
            ->orderByRaw('date(created_at)')
            ->pluck('total')
            ->all();
    }
}
