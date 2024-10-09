<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Database\Query\Builder;

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

        if ((int) $components->total - (int) $components->operational === 0) {
            $incidents = $this->incidents();

            if ((int) $incidents->total === 0 || ((int) $incidents->total > 0 && (int) $incidents->unresolved === 0)) {
                return SystemStatusEnum::operational;
            }
        }

        return SystemStatusEnum::partial_outage;
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
     * Get an overview of the components.
     *
     * @return object{total: int, operational: int, performance_issues: int, partial_outage: int, major_outage: int}
     */
    public function components()
    {
        return $this->components ??= Component::query()
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as operational', [ComponentStatusEnum::operational])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as performance_issues', [ComponentStatusEnum::performance_issues])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as partial_outage', [ComponentStatusEnum::partial_outage])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as major_outage', [ComponentStatusEnum::major_outage])
            // @todo Handle authenticated users.
            ->first();
    }

    /**
     * Get an overview of the incidents.
     *
     * @return object{total: int, resolved: int, unresolved: int}
     */
    public function incidents()
    {
        return $this->incidents ??= Incident::query()
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when ? in (incidents.status, latest_update.status) then 1 else 0 end) as resolved', [IncidentStatusEnum::fixed])
            ->selectRaw('sum(case when ? not in (incidents.status, latest_update.status) then 1 else 0 end) as unresolved', [IncidentStatusEnum::fixed])
            ->joinSub(function (Builder $query) {
                $query
                    ->select('iu1.incident_id', 'iu1.status')
                    ->from('incident_updates', 'iu1')
                    ->joinSub(function (Builder $query) {
                        $query->select('incident_id')
                            ->selectRaw('max(id) as max_id')
                            ->from('incident_updates')
                            ->groupBy('incident_id');
                    }, 'iu2', 'iu1.id', '=', 'iu2.max_id');
            }, 'latest_update', 'latest_update.incident_id', '=', 'incidents.id')
            ->first();
    }
}
