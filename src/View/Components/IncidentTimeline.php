<?php

namespace Cachet\View\Components;

use Cachet\Models\Incident;
use Cachet\Models\Schedule;
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
        $startDate = rescue(
            fn () => Carbon::createFromFormat('Y-m-d', request('from', now()->toDateString()))->startOfDay(),
            fn () => now()->startOfDay(),
            report: false
        );
        $endDate = $startDate->clone()->subDays($incidentDays);

        return view('cachet::components.incident-timeline', [
            'timeline' => $this->timeline(
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
     * Build the timeline of incidents and completed maintenance, grouped by day.
     *
     * @return Collection<string, array{incidents: Collection<int, Incident>, schedules: Collection<int, Schedule>}>
     */
    private function timeline(Carbon $startDate, Carbon $endDate, bool $onlyDisruptedDays = false): Collection
    {
        $incidents = $this->incidents($startDate, $endDate);
        $schedules = $this->schedules($startDate, $endDate);

        return collect($endDate->toPeriod($startDate))
            ->keyBy(fn ($period) => $period->toDateString())
            ->map(fn ($period) => collect())
            ->union($incidents)
            ->union($schedules)
            ->keys()
            ->mapWithKeys(fn (string $date) => [$date => [
                'incidents' => $incidents->get($date, collect()),
                'schedules' => $schedules->get($date, collect()),
            ]])
            ->when($onlyDisruptedDays, fn ($collection) => $collection->filter(
                fn (array $day) => $day['incidents']->isNotEmpty() || $day['schedules']->isNotEmpty()
            ))
            ->sortKeysDesc();
    }

    /**
     * Fetch the incidents that occurred between the given start and end date.
     * Incidents will be grouped by days.
     *
     * @return Collection<string, Collection<int, Incident>>
     */
    private function incidents(Carbon $startDate, Carbon $endDate): Collection
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
            ->groupBy(fn (Incident $incident) => $incident->timestamp->toDateString());
    }

    /**
     * Fetch the completed maintenance that occurred between the given start and end date.
     * Schedules will be grouped by the day they completed.
     *
     * @return Collection<string, Collection<int, Schedule>>
     */
    private function schedules(Carbon $startDate, Carbon $endDate): Collection
    {
        return Schedule::query()
            ->with(['components', 'updates' => fn ($query) => $query->orderByDesc('created_at')])
            ->inThePast()
            ->when($this->appSettings->recent_incidents_only, fn ($query) => $query->whereDate(
                'completed_at',
                '>',
                Carbon::now()->subDays($this->appSettings->recent_incidents_days)->format('Y-m-d')
            ))
            ->when(! $this->appSettings->recent_incidents_only, fn ($query) => $query->whereBetween('completed_at', [
                $endDate->startOfDay()->toDateTimeString(),
                $startDate->endofDay()->toDateTimeString(),
            ]))
            ->get()
            ->sortByDesc(fn (Schedule $schedule) => $schedule->completed_at)
            ->groupBy(fn (Schedule $schedule) => $schedule->completed_at->toDateString());
    }
}
