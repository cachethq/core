<?php

namespace Cachet;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Cachet\Settings\AppSettings;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class Status
{
    /**
     * How long a cached aggregate is served without being recalculated, in seconds.
     */
    private const CACHE_FRESH = 30;

    /**
     * How long a stale aggregate may still be served while it is recalculated
     * in the background, in seconds.
     */
    private const CACHE_TTL = 60;

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
        return $this->components ??= Cache::flexible(
            'cachet::status:components',
            [self::CACHE_FRESH, self::CACHE_TTL],
            fn (): object => $this->tally(),
        );
    }

    /**
     * Get an overview of the incidents visible to the public.
     *
     * @return object{total: int, resolved: int, unresolved: int}
     */
    public function incidents(): object
    {
        return $this->incidents ??= Cache::flexible(
            'cachet::status:incidents',
            [self::CACHE_FRESH, self::CACHE_TTL],
            fn (): object => Incident::query()
                ->viewableBy(false)
                ->toBase()
                ->selectRaw('count(*) as total')
                ->selectRaw('coalesce(sum(case when status = ? then 1 else 0 end), 0) as resolved', [IncidentStatusEnum::fixed->value])
                ->selectRaw('coalesce(sum(case when status is null or status <> ? then 1 else 0 end), 0) as unresolved', [IncidentStatusEnum::fixed->value])
                ->first(),
        );
    }

    /**
     * Get the most recent guest-visible activity timestamp across components,
     * incidents, incident updates and schedules.
     */
    public function lastUpdated(): ?CarbonInterface
    {
        return Cache::flexible(
            'cachet::status:last-updated',
            [self::CACHE_FRESH, self::CACHE_TTL],
            fn (): ?CarbonInterface => collect([
                Component::query()->enabled()->max('updated_at'),
                Incident::query()->viewableBy(false)->max('updated_at'),
                Schedule::query()->published()->max('updated_at'),
                Update::query()
                    ->where('updateable_type', Relation::getMorphAlias(Incident::class))
                    ->whereIn('updateable_id', Incident::query()->viewableBy(false)->select('id'))
                    ->max('updated_at'),
                Update::query()
                    ->where('updateable_type', Relation::getMorphAlias(Schedule::class))
                    ->whereIn('updateable_id', Schedule::query()->published()->select('id'))
                    ->max('updated_at'),
            ])
                ->filter()
                ->map(fn ($timestamp): CarbonInterface => Date::parse($timestamp))
                ->max(),
        );
    }

    /**
     * Forget the cached aggregates so the next read recalculates them.
     */
    public static function flush(): void
    {
        Cache::forget('cachet::status:components');
        Cache::forget('cachet::status:incidents');
        Cache::forget('cachet::status:last-updated');
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
