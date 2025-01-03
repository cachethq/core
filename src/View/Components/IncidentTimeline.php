<?php

namespace Cachet\View\Components;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class IncidentTimeline extends Component
{
    public function __construct(private AppSettings $appSettings)
    {
        //
    }

    public function render(): View
    {
        $incidentDays = $this->appSettings->recent_incidents_only ?
            $this->appSettings->recent_incidents_days - 1 :
            $this->appSettings->incident_days - 1;
        $startDate = Carbon::createFromFormat('Y-m-d', request('from', now()->toDateString()));
        $endDate = $startDate->clone()->subDays($incidentDays);

        return view('cachet::components.incident-timeline', [
            'incidents' => $this->incidents(
                $startDate,
                $endDate,
                $this->appSettings->only_disrupted_days
            ),
            'from' => $startDate->toDateString(),
            'to' => $endDate->toDateString(),
            'nextPeriodFrom' => $startDate->clone()->subDays($incidentDays + 1)->toDateString(),
            'nextPeriodTo' => $startDate->clone()->addDays($incidentDays + 1)->toDateString(),
            'canPageForward' => $this->appSettings->recent_incidents_only ? false : $startDate->clone()->isBefore(now()),
            'canPageBackward' => $this->appSettings->recent_incidents_only ? false : true,
            'recentIncidentsOnly' => $this->appSettings->recent_incidents_only,
        ]);
    }

    /**
     * Fetch the incidents that occurred between the given start and end date.
     * Incidents will be grouped by days.
     */
    private function incidents(Carbon $startDate, Carbon $endDate, bool $onlyDisruptedDays = false): Collection
    {
        return Incident::query()
            ->with([
                'components',
                'updates' => fn ($query) => $query->orderByDesc('created_at'),
            ])
            ->visible(auth()->check())
            ->when($this->appSettings->recent_incidents_only, function ($query) {
                $query->where(function ($query) {
                    $query->whereDate(
                        'occurred_at',
                        '>',
                        Carbon::now()->subDays($this->appSettings->recent_incidents_days)->format('Y-m-d')
                    )->orWhere(function ($query) {
                        $query->whereNull('occurred_at')->whereDate(
                            'created_at',
                            '>',
                            Carbon::now()->subDays($this->appSettings->recent_incidents_days)->format('Y-m-d')
                        );
                    });
                });
            })
            ->when(! $this->appSettings->recent_incidents_only, function ($query) use ($endDate, $startDate) {
                $query->where(function (Builder $query) use ($endDate, $startDate) {
                    $query->whereBetween('occurred_at', [
                        $endDate->startOfDay()->toDateTimeString(),
                        $startDate->endofDay()->toDateTimeString(),
                    ])->orWhere(function (Builder $query) use ($endDate, $startDate) {
                        $query->whereNull('occurred_at')->whereBetween('created_at', [
                            $endDate->startOfDay()->toDateTimeString(),
                            $startDate->endofDay()->toDateTimeString(),
                        ]);
                    });
                });
            })
            ->get()
            ->sortByDesc(fn (Incident $incident) => $incident->timestamp)
            ->groupBy(fn (Incident $incident) => $incident->timestamp->toDateString())
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
