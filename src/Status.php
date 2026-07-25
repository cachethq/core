<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Support\Collection;

class Status
{
    protected ?object $components = null;

    protected ?object $incidents = null;

    /**
     * Get the current system status as an enum.
     */
    public function current(): SystemStatusEnum
    {
        $components = $this->components();

        if ($this->majorOutage()) {
            return SystemStatusEnum::major_outage;
        }

        if ($this->underMaintenance()) {
            return SystemStatusEnum::under_maintenance;
        }

        if ((int) $components->total - (int) $components->operational === 0) {
            $incidents = $this->incidents();

            if ((int) $incidents->total === 0 || ((int) $incidents->total > 0 && (int) $incidents->unresolved === 0)) {
                return SystemStatusEnum::operational;
            }
        }

        return SystemStatusEnum::partial_outage;
    }

    /**
     * Determine if the system is under maintenance.
     */
    public function underMaintenance(): bool
    {
        if ((int) $this->components()->total === 0) {
            return false;
        }

        return (int) $this->components()->under_maintenance >= 1;
    }

    /**
     * Determine if there is a major outage.
     */
    public function majorOutage(): bool
    {
        if ((int) $this->components()->total === 0) {
            return false;
        }

        $majorOutageRate = app(AppSettings::class)->major_outage_threshold;

        return ((int) $this->components()->major_outage / (int) $this->components()->total) * 100 >= $majorOutageRate;
    }

    /**
     * Get an overview of the components, counted by their effective status.
     *
     * Effective status is resolved per component so that incident impacts and
     * maintenance windows reach the system status, rather than the banner
     * disagreeing with the component list it sits above.
     *
     * @return object{total: int, operational: int, performance_issues: int, partial_outage: int, major_outage: int, under_maintenance: int}
     */
    public function components(): object
    {
        return $this->components ??= $this->countByEffectiveStatus(
            Component::query()
                ->where('enabled', true)
                ->with(['unresolvedIncidents', 'activeMaintenance'])
                ->get()
        );
    }

    /**
     * Get an overview of the incidents visible to the public.
     *
     * @return object{total: int, resolved: int, unresolved: int}
     */
    public function incidents(): object
    {
        return $this->incidents ??= Incident::query()
            ->viewableBy(false)
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('coalesce(sum(case when status = ? then 1 else 0 end), 0) as resolved', [IncidentStatusEnum::fixed->value])
            ->selectRaw('coalesce(sum(case when status is null or status <> ? then 1 else 0 end), 0) as unresolved', [IncidentStatusEnum::fixed->value])
            ->first();
    }

    /**
     * Tally the given components by their effective status.
     *
     * @param  Collection<int, Component>  $components
     * @return object{total: int, operational: int, performance_issues: int, partial_outage: int, major_outage: int, under_maintenance: int}
     */
    private function countByEffectiveStatus(Collection $components): object
    {
        $statuses = $components->map(fn (Component $component) => $component->latest_status);

        return (object) [
            'total' => $components->count(),
            'operational' => $statuses->filter(fn (ComponentStatusEnum $status) => $status === ComponentStatusEnum::operational)->count(),
            'performance_issues' => $statuses->filter(fn (ComponentStatusEnum $status) => $status === ComponentStatusEnum::performance_issues)->count(),
            'partial_outage' => $statuses->filter(fn (ComponentStatusEnum $status) => $status === ComponentStatusEnum::partial_outage)->count(),
            'major_outage' => $statuses->filter(fn (ComponentStatusEnum $status) => $status === ComponentStatusEnum::major_outage)->count(),
            'under_maintenance' => $statuses->filter(fn (ComponentStatusEnum $status) => $status === ComponentStatusEnum::under_maintenance)->count(),
        ];
    }
}
