<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Illuminate\Database\Query\Builder;

class Status
{
    protected $components = null;

    protected $incidents = null;

    public function current()
    {
        $components = $this->components();

        if ($this->majorOutage()) {
            return [
                'status' => SystemStatusEnum::major_outage,
                'label' => SystemStatusEnum::major_outage->getLabel(),
                'color' => SystemStatusEnum::major_outage->getColor(),
            ];
        }

        if ($components->total - $components->operational === 0) {
            $incidents = $this->incidents();

            if ($incidents->total === 0 || ($incidents->total > 0 && $incidents->unresolved === 0)) {
                return [
                    'status' => SystemStatusEnum::operational,
                    'label' => SystemStatusEnum::operational->getLabel(),
                    'color' => SystemStatusEnum::operational->getColor(),
                ];
            }
        }

        return [
            'status' => SystemStatusEnum::partial_outage,
            'label' => SystemStatusEnum::partial_outage->getLabel(),
            'color' => SystemStatusEnum::partial_outage->getColor(),
        ];
    }

    public function majorOutage(): bool
    {
        if ($this->components()->total === 0) {
            return false;
        }

        $majorOutageRate = (int) config('cachet.major_outage_rate', 25);

        return ($this->components()->majorOutage / $this->components()->total) * 100 >= $majorOutageRate;
    }

    /**
     * Get an overview of the components.
     *
     * @return object{total: int, operational: int, performanceIssues: int, partialOutage: int, majorOutage: int}
     */
    public function components()
    {
        return $this->components ??= Component::query()
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as operational', [ComponentStatusEnum::operational])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as performanceIssues', [ComponentStatusEnum::performance_issues])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as partialOutage', [ComponentStatusEnum::partial_outage])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as majorOutage', [ComponentStatusEnum::major_outage])
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
