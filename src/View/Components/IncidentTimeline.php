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
            'stickiedIncidents' => $this->stickiedIncidents(),
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

        $dates = $onlyDisruptedDays
            ? $incidents->keys()->merge($schedules->keys())->unique()
            : collect($endDate->toPeriod($startDate))->map(fn ($period) => $period->toDateString());

        return $dates
            ->mapWithKeys(fn (string $date) => [$date => [
                'incidents' => $incidents->get($date, collect()),
                'schedules' => $schedules->get($date, collect()),
            ]])
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
        $rangeStart = $endDate->clone()->startOfDay();
        $rangeEnd = $startDate->clone()->addDay()->startOfDay();

        return Incident::query()
            ->with([
                'components',
                'updates' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
            ])
            ->viewableBy(auth()->check())
            ->where('stickied', false)
            ->when($this->appSettings->recent_incidents_only, function ($query) {
                $cutoff = $this->recentCutoff();

                $query->where(function ($query) use ($cutoff) {
                    $query->where('occurred_at', '>=', $cutoff)
                        ->orWhere(function ($query) use ($cutoff) {
                            $query->whereNull('occurred_at')->where('created_at', '>=', $cutoff);
                        });
                });
            })
            ->when(! $this->appSettings->recent_incidents_only, function ($query) use ($rangeEnd, $rangeStart) {
                $query->where(function (Builder $query) use ($rangeEnd, $rangeStart) {
                    $query->where(function (Builder $query) use ($rangeEnd, $rangeStart) {
                        $query->where('occurred_at', '>=', $rangeStart)
                            ->where('occurred_at', '<', $rangeEnd);
                    })->orWhere(function (Builder $query) use ($rangeEnd, $rangeStart) {
                        $query->whereNull('occurred_at')
                            ->where('created_at', '>=', $rangeStart)
                            ->where('created_at', '<', $rangeEnd);
                    });
                });
            })
            ->get()
            ->toBase()
            ->sortByDesc(fn (Incident $incident) => $incident->timestamp)
            ->groupBy(fn (Incident $incident) => $incident->timestamp->toDateString());
    }

    /**
     * Fetch incidents pinned to the top of the timeline.
     *
     * @return Collection<int, Incident>
     */
    private function stickiedIncidents(): Collection
    {
        return Incident::query()
            ->with([
                'components',
                'updates' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
            ])
            ->viewableBy(auth()->check())
            ->stickied()
            ->get()
            ->sortByDesc(fn (Incident $incident) => $incident->timestamp);
    }

    /**
     * Fetch the completed maintenance that occurred between the given start and end date.
     * Schedules will be grouped by the day they completed.
     *
     * @return Collection<string, Collection<int, Schedule>>
     */
    private function schedules(Carbon $startDate, Carbon $endDate): Collection
    {
        $rangeStart = $endDate->clone()->startOfDay();
        $rangeEnd = $startDate->clone()->addDay()->startOfDay();

        return Schedule::query()
            ->with(['components', 'updates' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id')])
            ->published()
            ->inThePast()
            ->when($this->appSettings->recent_incidents_only, fn ($query) => $query->where('completed_at', '>=', $this->recentCutoff()))
            ->when(! $this->appSettings->recent_incidents_only, fn ($query) => $query
                ->where('completed_at', '>=', $rangeStart)
                ->where('completed_at', '<', $rangeEnd))
            ->get()
            ->toBase()
            ->sortByDesc(fn (Schedule $schedule) => $schedule->completed_at)
            ->groupBy(fn (Schedule $schedule) => $schedule->completed_at->toDateString());
    }

    private function recentCutoff(): Carbon
    {
        return Carbon::now()
            ->subDays(max(0, $this->appSettings->recent_incidents_days - 1))
            ->startOfDay();
    }
}
