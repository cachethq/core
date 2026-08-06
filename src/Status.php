<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class Status
{
    protected ?object $components = null;

    protected ?object $incidents = null;

    /**
     * Get the current system status as an enum.
     *
     * There is deliberately no dedicated system status for performance issues:
     * components in a performance_issues (or partial_outage) state roll up to
     * SystemStatusEnum::partial_outage, which is returned as the default below.
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
        return $this->components ??= $this->tally();
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
     * Get the most recent update timestamp across enabled components.
     */
    public function lastUpdated(): ?CarbonInterface
    {
        return Component::query()
            ->enabled()
            ->latest('updated_at')
            ->first(['updated_at'])?->updated_at;
    }

    /**
     * Tally the enabled components by their effective status.
     *
     * The database counts the baseline, which is all that most components have.
     * Only those actually carrying an impact — an unresolved incident or a
     * maintenance window in progress — are loaded, and each is moved out of its
     * baseline bucket and into its effective one.
     *
     * @return object{total: int, operational: int, performance_issues: int, partial_outage: int, major_outage: int, under_maintenance: int}
     */
    private function tally(): object
    {
        $counts = Component::query()
            ->enabled()
            ->toBase()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $tally = collect(ComponentStatusEnum::cases())
            ->mapWithKeys(fn (ComponentStatusEnum $status) => [
                $status->name => (int) $counts->get($status->value, 0),
            ]);

        $total = $tally->sum();

        Component::query()
            ->enabled()
            ->where(fn (Builder $query) => $query
                ->whereHas('unresolvedIncidents')
                ->orWhereHas('activeMaintenance'))
            ->with(['unresolvedIncidents', 'activeMaintenance'])
            ->each(function (Component $component) use (&$tally): void {
                $baseline = $component->status;
                $effective = $component->latest_status;

                if ($baseline === $effective) {
                    return;
                }

                $tally[$baseline->name] = $tally[$baseline->name] - 1;
                $tally[$effective->name] = $tally[$effective->name] + 1;
            });

        return (object) [...$tally->all(), 'total' => $total];
    }
}
