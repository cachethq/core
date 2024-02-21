<?php

namespace Cachet\View\Components;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Contracts\View\View;
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
        return view('cachet::components.incidents', [
            'incidents' => $this->incidents(),
        ]);
    }

    private function incidents(): Collection
    {
        $startDate = now()->subDays($this->appSettings->incident_days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        return Incident::query()
            ->with([
                'components',
                'incidentUpdates' => fn ($query) => $query->orderByDesc('created_at'),
            ])
            ->where('visible', '>=', ! auth()->check())
            ->whereBetween('occurred_at', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->orderBy('occurred_at', 'desc')
            ->get()
            ->groupBy(fn (Incident $incident) => $incident->occurred_at?->toDateString())
            ->union(
                // Back-fill any missing dates...
                collect($startDate->toPeriod($endDate))
                    ->keyBy(fn ($period) => $period->toDateString())
                    ->map(fn ($period) => [])
            )
            ->sortKeysDesc();
    }
}
