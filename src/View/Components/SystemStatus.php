<?php

namespace Cachet\View\Components;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\ComponentStatusChange;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\Component as ViewComponent;
use Illuminate\View\View;

/**
 * @phpstan-type StatusDays Collection<string, ComponentStatusEnum|null>
 * @phpstan-type ComponentDays Collection<string, ComponentStatusEnum>
 * @phpstan-type DayEvent array{label: string, status: ComponentStatusEnum, icon: string}
 * @phpstan-type DayEvents Collection<string, list<DayEvent>>
 * @phpstan-type ComponentRow array{model: Component, status: ComponentStatusEnum, days: ComponentDays, events: DayEvents, uptime: float|null, available_seconds: float, operational_seconds: float}
 * @phpstan-type GroupRow array{model: ComponentGroup, components: Collection<int, ComponentRow>, days: StatusDays, events: DayEvents, uptime: float|null, available_seconds: float, operational_seconds: float}
 */
class SystemStatus extends ViewComponent
{
    private const DAYS = 90;

    private Carbon $start;

    private Carbon $end;

    public function __construct()
    {
        $this->end = now();
        $this->start = $this->end->clone()->subDays(self::DAYS - 1)->startOfDay();
    }

    public function render(): View|Closure|string
    {
        $groups = $this->groups();
        $ungroupedComponents = $this->components(
            Component::query()->enabled()->whereNull('component_group_id')->orderBy('order')
        );

        return view('cachet::components.system-status', [
            'from' => $this->start,
            'to' => $this->end,
            'groups' => $groups,
            'ungroupedComponents' => $ungroupedComponents,
        ]);
    }

    /**
     * Fetch visible groups and calculate their component and aggregate uptime.
     *
     * @return Collection<int, GroupRow>
     */
    private function groups(): Collection
    {
        return ComponentGroup::query()
            ->visible(auth()->check())
            ->orderBy('order')
            ->when(auth()->check(), fn (Builder $query) => $query->users(), fn ($query) => $query->guests())
            ->get()
            ->map(function (ComponentGroup $group): array {
                $components = $this->components(
                    $group->components()->enabled()->orderBy('order')
                );

                return [
                    'model' => $group,
                    'components' => $components,
                    'days' => $this->aggregateDays($components),
                    'events' => $this->aggregateEvents($components),
                    ...$this->aggregateUptime($components),
                ];
            })
            ->filter(fn (array $group): bool => $group['components']->isNotEmpty())
            ->values();
    }

    /**
     * Load the history needed to calculate each component's uptime.
     *
     * @param  Builder<Component>|HasMany<Component, ComponentGroup>  $query
     * @return Collection<int, ComponentRow>
     */
    private function components(Builder|HasMany $query): Collection
    {
        return $query
            ->with([
                'statusChanges' => fn ($query) => $query->where('created_at', '>=', $this->start)->orderBy('created_at'),
                'incidents' => fn ($query) => $query
                    ->viewableBy(false)
                    ->where(function ($query): void {
                        $query->where('incidents.occurred_at', '>=', $this->start)
                            ->orWhere('incidents.created_at', '>=', $this->start)
                            ->orWhereIn('incidents.status', IncidentStatusEnum::unresolved())
                            ->orWhereHas('updates', fn ($query) => $query->where('updates.created_at', '>=', $this->start));
                    })
                    ->with('updates'),
                'schedules' => fn ($query) => $query
                    ->published()
                    ->where('scheduled_at', '<=', $this->end)
                    ->where(fn ($query) => $query->whereNull('completed_at')->orWhere('completed_at', '>=', $this->start)),
            ])
            ->get()
            ->map(function (Component $component): array {
                $uptime = $this->uptime($component);

                return [
                    'model' => $component,
                    'status' => $this->statusAt($component, $this->end) ?? ComponentStatusEnum::unknown,
                    'days' => $uptime['days'],
                    'events' => $this->events($component, $uptime['days']),
                    'uptime' => $uptime['uptime'],
                    'available_seconds' => $uptime['available_seconds'],
                    'operational_seconds' => $uptime['operational_seconds'],
                ];
            });
    }

    /**
     * Calculate daily severity and exact uptime from every effective status interval.
     *
     * @return array{days: ComponentDays, uptime: float|null, available_seconds: float, operational_seconds: float}
     */
    private function uptime(Component $component): array
    {
        $days = collect($this->start->toPeriod($this->end))
            ->keyBy(fn (Carbon $day): string => $day->toDateString())
            ->map(fn (): ComponentStatusEnum => $this->noDataStatus());
        $boundaries = collect([$this->start, $this->end, $component->created_at])
            ->merge($component->statusChanges->pluck('created_at'))
            ->merge($component->incidents->flatMap(fn (Incident $incident): array => [
                $incident->timestamp,
                $this->incidentEnd($incident),
            ]))
            ->merge($component->schedules->flatMap(fn (Schedule $schedule): array => [
                $schedule->scheduled_at,
                $schedule->completed_at ?? $this->end,
            ]))
            ->merge($this->start->toPeriod($this->end)->map(fn (Carbon $day): Carbon => $day->clone()->startOfDay()))
            ->filter(fn (?CarbonInterface $boundary): bool => $boundary !== null && $boundary->betweenIncluded($this->start, $this->end))
            ->push($this->end)
            ->unique(fn (CarbonInterface $boundary): string => $boundary->toISOString())
            ->sort()
            ->values();
        $availableSeconds = 0.0;
        $operationalSeconds = 0.0;

        $boundaries->sliding(2)->each(function (Collection $interval) use ($component, $days, &$availableSeconds, &$operationalSeconds): void {
            [$from, $to] = $interval->values();
            $status = $this->statusAt($component, $from);

            if ($status === null || $status === ComponentStatusEnum::unknown) {
                return;
            }

            $seconds = $from->diffInSeconds($to);
            $availableSeconds += $seconds;
            $operationalSeconds += $status === ComponentStatusEnum::operational ? $seconds : 0;
            $date = $from->toDateString();
            $days->put($date, $this->worstStatus(collect([$days->get($date), $status])));
        });

        return [
            'days' => $days,
            'uptime' => $availableSeconds > 0 ? ($operationalSeconds / $availableSeconds) * 100 : null,
            'available_seconds' => $availableSeconds,
            'operational_seconds' => $operationalSeconds,
        ];
    }

    private function noDataStatus(): ComponentStatusEnum
    {
        return ComponentStatusEnum::unknown;
    }

    /**
     * Collect the public events that explain each day's indicator.
     *
     * @param  ComponentDays  $days
     * @return DayEvents
     */
    private function events(Component $component, Collection $days): Collection
    {
        return $days->map(function (ComponentStatusEnum $status, string $date) use ($component): array {
            $start = Carbon::parse($date)->startOfDay();
            $end = $start->clone()->addDay();

            $incidents = $component->incidents
                ->filter(fn (Incident $incident): bool => $incident->timestamp->lt($end) && $this->incidentEnd($incident)->gt($start))
                ->map(fn (Incident $incident): array => [
                    'label' => $incident->name,
                    'status' => $this->impactStatus($incident->pivot->component_status),
                    'icon' => 'cachet-incident',
                ])
                ->toBase();
            $schedules = $component->schedules
                ->filter(fn (Schedule $schedule): bool => $schedule->scheduled_at?->lt($end) === true && ($schedule->completed_at === null || $schedule->completed_at->gt($start)))
                ->map(fn (Schedule $schedule): array => [
                    'label' => $schedule->name,
                    'status' => $this->impactStatus($schedule->pivot->component_status),
                    'icon' => 'cachet-maintenance',
                ])
                ->toBase();
            $changes = $component->statusChanges
                ->filter(fn (ComponentStatusChange $change): bool => $change->created_at?->gte($start) === true && $change->created_at->lt($end))
                ->map(fn (ComponentStatusChange $change): array => [
                    'label' => trans_choice('cachet::system_status.status_changed', 1, ['status' => $change->new_status->getLabel()]),
                    'status' => $change->new_status,
                    'icon' => $change->new_status->getIcon(),
                ]);

            return array_values($incidents
                ->merge($schedules)
                ->merge($changes)
                ->unique(fn (array $event): string => $event['label'].'-'.$event['status']->value.'-'.$event['icon'])
                ->values()
                ->all());
        });
    }

    private function impactStatus(mixed $status): ComponentStatusEnum
    {
        if ($status instanceof ComponentStatusEnum) {
            return $status;
        }

        return ComponentStatusEnum::tryFrom((int) $status) ?? ComponentStatusEnum::unknown;
    }

    private function statusAt(Component $component, CarbonInterface $time): ?ComponentStatusEnum
    {
        if ($component->created_at === null || $time->lt($component->created_at)) {
            return null;
        }

        $effectiveChanges = $component->statusChanges
            ->filter(fn ($change): bool => $change->created_at->lte($time));

        if ($effectiveChanges->isNotEmpty()) {
            $baseline = $effectiveChanges->last()->new_status;
        } elseif ($component->statusChanges->isNotEmpty()) {
            $baseline = $component->statusChanges->first()->old_status ?? $component->status;
        } else {
            $baseline = $component->status;
        }
        $incidentStatuses = $component->incidents
            ->filter(fn (Incident $incident): bool => $incident->timestamp->lte($time) && $this->incidentEnd($incident)->gt($time))
            ->pluck('pivot.component_status');
        $scheduleStatuses = $component->schedules
            ->filter(fn (Schedule $schedule): bool => $schedule->scheduled_at?->lte($time) === true && ($schedule->completed_at === null || $schedule->completed_at->gt($time)))
            ->pluck('pivot.component_status');

        return $this->worstStatus(collect([$baseline])->merge($incidentStatuses)->merge($scheduleStatuses));
    }

    private function incidentEnd(Incident $incident): CarbonInterface
    {
        $fixedAt = $incident->updates
            ->where('status', IncidentStatusEnum::fixed)
            ->sortBy('created_at')
            ->first()
            ?->created_at;

        if ($fixedAt !== null) {
            return $fixedAt;
        }

        return $incident->status === IncidentStatusEnum::fixed
            ? ($incident->updated_at ?? $incident->created_at)
            : $this->end;
    }

    /**
     * @param  iterable<ComponentStatusEnum|null>  $statuses
     */
    private function worstStatus(iterable $statuses): ?ComponentStatusEnum
    {
        return collect($statuses)
            ->filter(fn (?ComponentStatusEnum $status): bool => $status !== null && $status !== ComponentStatusEnum::unknown)
            ->sortByDesc(fn (ComponentStatusEnum $status): int => $status->severity())
            ->first();
    }

    /**
     * @param  Collection<int, ComponentRow>  $components
     * @return StatusDays
     */
    private function aggregateDays(Collection $components): Collection
    {
        return collect($this->start->toPeriod($this->end))->mapWithKeys(function (Carbon $day) use ($components): array {
            $date = $day->toDateString();

            return [$date => $this->worstStatus($components->pluck('days')->map(fn (Collection $days) => $days->get($date)))];
        });
    }

    /**
     * @param  Collection<int, ComponentRow>  $components
     * @return DayEvents
     */
    private function aggregateEvents(Collection $components): Collection
    {
        return collect($this->start->toPeriod($this->end))->mapWithKeys(function (Carbon $day) use ($components): array {
            $date = $day->toDateString();
            $events = array_values($components
                ->pluck('events')
                ->flatMap(fn (Collection $events): array => $this->eventsForDate($events, $date))
                ->unique(fn (array $event): string => $event['label'].'-'.$event['status']->value.'-'.$event['icon'])
                ->values()
                ->all());

            return [$date => $events];
        });
    }

    /**
     * @param  DayEvents  $events
     * @return list<DayEvent>
     */
    private function eventsForDate(Collection $events, string $date): array
    {
        return $events->get($date, []);
    }

    /**
     * @param  Collection<int, ComponentRow>  $components
     * @return array{uptime: float|null, available_seconds: float, operational_seconds: float}
     */
    private function aggregateUptime(Collection $components): array
    {
        $availableSeconds = $components->sum('available_seconds');
        $operationalSeconds = $components->sum('operational_seconds');

        return [
            'uptime' => $availableSeconds > 0 ? ($operationalSeconds / $availableSeconds) * 100 : null,
            'available_seconds' => $availableSeconds,
            'operational_seconds' => $operationalSeconds,
        ];
    }
}
