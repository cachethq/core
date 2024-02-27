<?php

namespace Cachet\View\Components;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Incidents extends Component
{
    public function __construct(private AppSettings $appSettings)
    {
        //
    }

    public function render(): View
    {
        $incidentDays = $this->appSettings->incident_days - 1;
        $startDate = Carbon::createFromFormat('Y-m-d', request('from', now()->toDateString()));
        $endDate = $startDate->clone()->subDays($incidentDays);

        return view('cachet::components.incidents', [
            'incidents' => $this->incidents(
                $startDate,
                $endDate,
                $this->appSettings->only_disrupted_days
            ),
            'from' => $startDate->clone()->subDays($incidentDays)->toDateString(),
            'to' => $startDate->clone()->addDays($incidentDays)->toDateString(),
        ]);
    }

    private function incidents(Carbon $startDate, Carbon $endDate, bool $onlyDisruptedDays = false): Collection
    {
        return Incident::query()
            ->with([
                'components',
                'incidentUpdates' => fn ($query) => $query->orderByDesc('created_at'),
            ])
            ->where('visible', '>=', ! auth()->check())
            ->whereBetween('occurred_at', [
                $endDate->startOfDay()->toDateTimeString(),
                $startDate->endofDay()->toDateTimeString(),
            ])
            ->orderBy('occurred_at', 'desc')
            ->get()
            ->groupBy(fn (Incident $incident) => $incident->occurred_at?->toDateString())
            ->union(
                // Back-fill any missing dates...
                collect($endDate->toPeriod($startDate))
                    ->keyBy(fn ($period) => $period->toDateString())
                    ->map(fn ($period) => collect())
            )
            ->when($onlyDisruptedDays, fn ($collection) => $collection->filter(fn ($incidents) => $incidents->isNotEmpty()))
            ->sortKeysDesc();
    }
}
