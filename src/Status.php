<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        if ($this->underMaintenance()) {
            return SystemStatusEnum::under_maintenance;
        }

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
     * Get an overview of the components.
     *
     * @return object{total: int, operational: int, performance_issues: int, partial_outage: int, major_outage: int}
     */
    public function components(): object
    {
        return $this->components ??= Component::query()
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as operational', [ComponentStatusEnum::operational])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as performance_issues', [ComponentStatusEnum::performance_issues])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as partial_outage', [ComponentStatusEnum::partial_outage])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as major_outage', [ComponentStatusEnum::major_outage])
            ->selectRaw('sum(case when status = ? then 1 else 0 end) as under_maintenance', [ComponentStatusEnum::under_maintenance])
            ->first();
    }

    /**
     * Get an overview of the incidents.
     *
     * @return object{total: int, resolved: int, unresolved: int}
     */
    public function incidents(): object
    {
        return $this->incidents ??= Incident::query()
            ->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when ? in (incidents.status, coalesce(latest_update.status, ?)) then 1 else 0 end) as resolved', [IncidentStatusEnum::fixed->value, 0])
            ->selectRaw('sum(case when ? not in (incidents.status, coalesce(latest_update.status, ?)) then 1 else 0 end) as unresolved', [IncidentStatusEnum::fixed->value, 0])
            ->joinSub(function (Builder $query) {
                $query
                    ->select('iu1.updateable_id', 'iu1.status')
                    ->from('updates', 'iu1')
                    ->joinSub(function (Builder $query) {
                        $query->select('updateable_id')
                            ->selectRaw('max(id) as max_id')
                            ->from('updates')
                            ->where('updates.updateable_type', Relation::getMorphAlias(Incident::class))
                            ->groupBy('updateable_id');
                    }, 'iu2', 'iu1.id', '=', 'iu2.max_id', 'left');
            }, 'latest_update', 'latest_update.updateable_id', '=', 'incidents.id', 'left')
            ->first();
    }
}
