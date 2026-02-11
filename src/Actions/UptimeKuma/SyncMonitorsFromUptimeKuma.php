<?php

namespace Cachet\Actions\UptimeKuma;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Services\UptimeKuma\UptimeKumaClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Sync monitors and groups from Uptime Kuma to Cachet.
 *
 * Mapping:
 * - Uptime Kuma "Group" → Cachet "Component Group"
 * - Uptime Kuma "Monitor" → Cachet "Component"
 */
class SyncMonitorsFromUptimeKuma
{
    /**
     * Uptime Kuma status codes mapped to Cachet component status.
     * 0 = DOWN, 1 = UP, 2 = PENDING, 3 = MAINTENANCE
     */
    protected const STATUS_MAP = [
        0 => ComponentStatusEnum::major_outage,      // DOWN
        1 => ComponentStatusEnum::operational,        // UP
        2 => ComponentStatusEnum::unknown,            // PENDING
        3 => ComponentStatusEnum::under_maintenance,  // MAINTENANCE
    ];

    public function __construct(
        protected UptimeKumaClient $client
    ) {}

    /**
     * Sync all groups and monitors from Uptime Kuma to Cachet.
     *
     * @return array{groups_created: int, groups_updated: int, components_created: int, components_updated: int, errors: array}
     */
    public function handle(bool $updateStatus = true): array
    {
        $result = [
            'groups_created' => 0,
            'groups_updated' => 0,
            'components_created' => 0,
            'components_updated' => 0,
            'total_synced' => 0,
            'errors' => [],
        ];

        if (! config('cachet.uptime_kuma.enabled', true)) {
            $result['errors'][] = 'Uptime Kuma integration is disabled';

            return $result;
        }
        if (! $this->client->ping()) {
            $result['errors'][] = 'Cannot connect to Uptime Kuma at '.$this->client->getBaseUrl();

            return $result;
        }
        $groups = $this->client->getGroupsWithMonitors();

        if (empty($groups)) {
            $result['errors'][] = 'No groups/monitors found. Make sure you have a public status page configured in Uptime Kuma with the slug: '.$this->client->getStatusPageSlug();

            return $result;
        }

        Log::info('Syncing from Uptime Kuma', [
            'groups' => count($groups),
            'monitors' => array_sum(array_map(fn ($g) => count($g['monitors']), $groups)),
        ]);

        foreach ($groups as $group) {
            try {
                $syncResult = $this->syncGroup($group, $updateStatus);
                $result['groups_created'] += $syncResult['group_created'] ? 1 : 0;
                $result['groups_updated'] += $syncResult['group_updated'] ? 1 : 0;
                $result['components_created'] += $syncResult['components_created'];
                $result['components_updated'] += $syncResult['components_updated'];
                $result['total_synced'] += $syncResult['components_created'] + $syncResult['components_updated'];
            } catch (\Exception $e) {
                $result['errors'][] = "Failed to sync group {$group['id']}: ".$e->getMessage();
                Log::error('Failed to sync Uptime Kuma group', [
                    'group_id' => $group['id'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('sync completed', $result);

        return $result;
    }

    /**
     * Sync a single group and its monitors.
     *
     * @return array{group_created: bool, group_updated: bool, components_created: int, components_updated: int}
     */
    protected function syncGroup(array $group, bool $updateStatus): array
    {
        $result = [
            'group_created' => false,
            'group_updated' => false,
            'components_created' => 0,
            'components_updated' => 0,
        ];

        $uptimeKumaGroupId = $group['id'];
        $componentGroup = ComponentGroup::query()
            ->whereJsonContains('meta->uptime_kuma_group_id', $uptimeKumaGroupId)
            ->first();

        if (! $componentGroup) {
            $componentGroup = ComponentGroup::query()
                ->whereRaw("json_extract(meta, '$.uptime_kuma_group_id') = ?", [$uptimeKumaGroupId])
                ->first();
        }

        if ($componentGroup) {
            $componentGroup->update([
                'name' => $group['name'],
                'order' => $group['weight'],
                'meta' => array_merge($componentGroup->meta ?? [], [
                    'uptime_kuma_group_id' => $uptimeKumaGroupId,
                    'uptime_kuma_last_sync' => now()->toIso8601String(),
                ]),
            ]);
            $result['group_updated'] = true;
        } else {
            $componentGroup = ComponentGroup::create([
                'name' => $group['name'],
                'order' => $group['weight'],
                'visible' => ResourceVisibilityEnum::guest,
                'collapsed' => ComponentGroupVisibilityEnum::expanded,
                'meta' => [
                    'uptime_kuma_group_id' => $uptimeKumaGroupId,
                    'uptime_kuma_last_sync' => now()->toIso8601String(),
                ],
            ]);
            $result['group_created'] = true;
        }

        foreach ($group['monitors'] as $monitor) {
            try {
                $syncResult = $this->syncMonitor($monitor, $componentGroup->id, $updateStatus);
                if ($syncResult === 'created') {
                    $result['components_created']++;
                } else {
                    $result['components_updated']++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to sync monitor', [
                    'monitor_id' => $monitor['id'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    /**
     * Sync a single monitor to a Cachet component.
     */
    protected function syncMonitor(array $monitor, int $componentGroupId, bool $updateStatus): string
    {
        $monitorId = $monitor['id'];
        $component = Component::query()
            ->whereRaw("json_extract(meta, '$.uptime_kuma_monitor_id') = ?", [$monitorId])
            ->first();

        if ($component) {
            $updates = [
                'component_group_id' => $componentGroupId,
            ];

            if ($updateStatus) {
                $updates['status'] = $this->mapStatus($monitor['status']);
            }
            $meta = $component->meta ?? [];
            $meta['uptime_kuma_monitor_id'] = $monitorId;
            $meta['uptime_kuma_type'] = $monitor['type'] ?? 'http';
            $meta['uptime_kuma_last_sync'] = now()->toIso8601String();
            if (isset($monitor['uptime_24h'])) {
                $meta['uptime_kuma_uptime_24h'] = $monitor['uptime_24h'];
            }
            if (isset($monitor['certExpiryDaysRemaining'])) {
                $meta['ssl_expiry_days'] = $monitor['certExpiryDaysRemaining'];
            }
            if (isset($monitor['heartbeats']) && ! empty($monitor['heartbeats'])) {
                $meta['heartbeats'] = $monitor['heartbeats'];
            }
            $updates['meta'] = $meta;

            $updates['description'] = $this->generateDescription($monitor);
            if (! empty($monitor['url']) && empty($component->link)) {
                $updates['link'] = $monitor['url'];
            }

            $component->update($updates);

            return 'updated';
        }

        $meta = [
            'uptime_kuma_monitor_id' => $monitorId,
            'uptime_kuma_type' => $monitor['type'] ?? 'http',
            'uptime_kuma_last_sync' => now()->toIso8601String(),
        ];

        if (isset($monitor['uptime_24h'])) {
            $meta['uptime_kuma_uptime_24h'] = $monitor['uptime_24h'];
        }

        if (isset($monitor['certExpiryDaysRemaining'])) {
            $meta['ssl_expiry_days'] = $monitor['certExpiryDaysRemaining'];
        }
        if (isset($monitor['heartbeats']) && ! empty($monitor['heartbeats'])) {
            $meta['heartbeats'] = $monitor['heartbeats'];
        }

        Component::create([
            'name' => $monitor['name'] ?? "Monitor {$monitorId}",
            'description' => $this->generateDescription($monitor),
            'link' => $monitor['url'] ?? null,
            'status' => $this->mapStatus($monitor['status']),
            'component_group_id' => $componentGroupId,
            'enabled' => true,
            'order' => $monitorId,
            'meta' => $meta,
        ]);

        return 'created';
    }

    /**
     * Map Uptime Kuma status to Cachet component status.
     */
    protected function mapStatus(int $status): ComponentStatusEnum
    {
        return self::STATUS_MAP[$status] ?? ComponentStatusEnum::unknown;
    }

    /**
     * Generate a  description for the component based on monitoring data
     */
    protected function generateDescription(array $monitor): string
    {
        $parts = [];
        if (isset($monitor['uptime_24h'])) {
            $uptime = round($monitor['uptime_24h'] * 100, 2);
            $parts[] = "24h Uptime: {$uptime}%";
        }
        if (isset($monitor['certExpiryDaysRemaining']) && $monitor['certExpiryDaysRemaining'] > 0) {
            $days = $monitor['certExpiryDaysRemaining'];
            $parts[] = "SSL: {$days} days remaining";
        }
        if (empty($parts)) {
            return 'Monitoring active. Status data will appear after first heartbeat.';
        }

        return implode(' | ', $parts);
    }

    /**
     * Sync status and heartbeat data for existing linked components.
     *
     *
     * @return array{updated: int, errors: array}
     */
    public function syncStatusOnly(): array
    {
        $result = [
            'updated' => 0,
            'errors' => [],
        ];

        if (! config('cachet.uptime_kuma.enabled', true)) {
            $result['errors'][] = 'Uptime Kuma integration is disabled';

            return $result;
        }

        //get all components linked to Uptime Kuma
        $components = Component::query()
            ->whereNotNull('meta->uptime_kuma_monitor_id')
            ->get();

        if ($components->isEmpty()) {
            return $result;
        }
        $monitors = collect($this->client->getMonitors())->keyBy('id');

        foreach ($components as $component) {
            $monitorId = $component->meta['uptime_kuma_monitor_id'] ?? null;

            if (! $monitorId) {
                continue;
            }

            $monitor = $monitors->get($monitorId);

            if (! $monitor) {
                continue;
            }

            $newStatus = $this->mapStatus($monitor['status']);

            $meta = $component->meta ?? [];
            $meta['uptime_kuma_monitor_id'] = $monitorId;
            $meta['uptime_kuma_last_sync'] = now()->toIso8601String();

            if (isset($monitor['uptime_24h'])) {
                $meta['uptime_kuma_uptime_24h'] = $monitor['uptime_24h'];
            }

            if (isset($monitor['certExpiryDaysRemaining'])) {
                $meta['ssl_expiry_days'] = $monitor['certExpiryDaysRemaining'];
            }
            if (isset($monitor['heartbeats']) && ! empty($monitor['heartbeats'])) {
                $meta['heartbeats'] = $monitor['heartbeats'];
            }

            $updates = [
                'status' => $newStatus,
                'meta' => $meta,
                'description' => $this->generateDescription($monitor),
            ];

            $component->update($updates);

            // If monitor is UP, resolve any active incidents created by Uptime Kuma
            if ($newStatus === ComponentStatusEnum::operational) {
                $this->resolveActiveIncidents($component);
            }

            $result['updated']++;
        }

        return $result;
    }

    /**
     * Resolve any active Uptime Kuma incidents for a component that is now operational.
     */
    protected function resolveActiveIncidents(Component $component): void
    {
        $activeIncidents = Incident::query()
            ->where('external_provider', 'uptime_kuma')
            ->whereIn('status', IncidentStatusEnum::unresolved())
            ->whereHas('components', fn ($query) => $query->where('components.id', $component->id))
            ->get();

        foreach ($activeIncidents as $incident) {
            $resolvedAt = Carbon::now();
            $duration = $incident->occurred_at
                ? $incident->occurred_at->diffForHumans($resolvedAt, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])
                : 'unknown duration';

            $incident->updates()->create([
                'status' => IncidentStatusEnum::fixed,
                'message' => "## Service Restored\n\n"
                    . "**Status:** Resolved\n\n"
                    . "**Resolved:** {$resolvedAt->format('F j, Y \a\t g:i A T')}\n\n"
                    . "**Total Downtime:** {$duration}\n\n"
                    . "---\n\n"
                    . "The service has been restored and is now operating normally.\n\n"
                    . "_This update was automatically generated during status sync._",
            ]);

            $incident->update(['status' => IncidentStatusEnum::fixed]);

            $incident->components()->updateExistingPivot(
                $incident->components->pluck('id')->toArray(),
                ['component_status' => ComponentStatusEnum::operational]
            );

            Log::info('Auto-resolved incident during sync', [
                'incident_id' => $incident->id,
                'component_id' => $component->id,
                'duration' => $duration,
            ]);
        }
    }

    /**
     * Get all linked components with their current Uptime Kuma status.
     */
    public function getLinkedComponents(): Collection
    {
        return Component::query()
            ->whereNotNull('meta->uptime_kuma_monitor_id')
            ->get()
            ->map(function (Component $component) {
                $monitorId = $component->meta['uptime_kuma_monitor_id'] ?? null;
                $monitor = $monitorId ? $this->client->getMonitorStatus($monitorId) : null;

                return [
                    'component' => $component,
                    'monitor_id' => $monitorId,
                    'uptime_kuma_status' => $monitor ? $this->mapStatus($monitor['status']) : null,
                    'synced' => $monitor !== null,
                ];
            });
    }
}
