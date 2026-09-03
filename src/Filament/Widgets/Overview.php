<?php

namespace Cachet\Filament\Widgets;

use Cachet\Filament\Resources\Components\ComponentResource;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Filament\Resources\Subscribers\SubscriberResource;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;
use Cachet\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class Overview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return Gate::allows('viewAny', Subscriber::class) ? 3 : 2;
    }

    protected function getStats(): array
    {
        $openIncidents = Incident::query()->unresolved()->count();
        $components = app(Status::class)->components();
        $totalComponents = (int) $components->total;
        $operationalComponents = (int) $components->operational;
        $allOperational = $totalComponents === $operationalComponents;

        $stats = [
            Stat::make('open_incidents', $openIncidents)
                ->label(__('cachet::incident.overview.open_incidents_label'))
                ->description(__('cachet::incident.overview.open_incidents_description'))
                ->chart($this->dailyCounts('incidents'))
                ->icon('cachet-incident')
                ->chartColor($openIncidents > 0 ? 'danger' : 'success')
                ->color($openIncidents > 0 ? 'danger' : 'success')
                ->url(IncidentResource::getUrl('index')),

            Stat::make('operational_components', "{$operationalComponents} / {$totalComponents}")
                ->label(__('cachet::component.overview.operational_components_label'))
                ->description(__('cachet::component.overview.operational_components_description'))
                ->icon('cachet-components')
                ->color($allOperational ? 'success' : 'warning')
                ->url(ComponentResource::getUrl('index')),

        ];

        if (Gate::denies('viewAny', Subscriber::class)) {
            return $stats;
        }

        $stats[] = Stat::make('total_subscribers', Subscriber::count())
            ->label(__('cachet::subscriber.overview.total_subscribers_label'))
            ->description(__('cachet::subscriber.overview.verified_subscribers_description', [
                'count' => Subscriber::query()->whereNotNull('email_verified_at')->count(),
            ]))
            ->chart($this->dailyCounts('subscribers'))
            ->icon('cachet-subscribers')
            ->chartColor('info')
            ->color('gray')
            ->url(SubscriberResource::getUrl('index'));

        return $stats;
    }

    /**
     * Get the number of records created per day over the last 30 days.
     *
     * @return array<int, int>
     */
    protected function dailyCounts(string $table): array
    {
        $today = now()->startOfDay();
        $startDate = $today->copy()->subDays(29);

        $totalsByDate = DB::table($table)
            ->selectRaw('date(created_at) as date, count(*) as total')
            ->whereBetween('created_at', [$startDate, $today->copy()->endOfDay()])
            ->groupByRaw('date(created_at)')
            ->orderByRaw('date(created_at)')
            ->pluck('total', 'date');

        return collect(range(29, 0))
            ->map(fn (int $daysAgo): int => (int) ($totalsByDate[$today->copy()->subDays($daysAgo)->toDateString()] ?? 0))
            ->all();
    }
}
