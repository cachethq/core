<?php

namespace Cachet\Services\UptimeKuma;

use Cachet\Settings\IntegrationSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for interacting with Uptime Kuma's public API.
 *
 * This client fetches data from status pages including groups (monitor groups) and monitors.
 *
 *
 * Mapping:
 * - Uptime Kuma "Group" → Cachet "Component Group"
 * - Uptime Kuma "Monitor" → Cachet "Component"
 */
class UptimeKumaClient
{
    protected string $baseUrl;

    protected string $statusPageSlug;

    /**
     * Create a new Uptime Kuma client instance.
     *
     * @param  string|null  $baseUrl  Override URL (optional)
     * @param  string|null  $statusPageSlug  Override slug (optional)
     */
    public function __construct(?string $baseUrl = null, ?string $statusPageSlug = null)
    {
        // Priority: constructor params > database settings > config/env > defaults
        $this->baseUrl = rtrim($this->resolveBaseUrl($baseUrl), '/');
        $this->statusPageSlug = $this->resolveStatusPageSlug($statusPageSlug);
    }

    /**
     * Resolve the base URL from multiple sources.
     */
    protected function resolveBaseUrl(?string $override): string
    {
        if ($override !== null && $override !== '') {
            return $override;
        }

        // Try to get from database settings
        try {
            $settings = app(IntegrationSettings::class);
            if ($settings->uptime_kuma_url !== null && $settings->uptime_kuma_url !== '') {
                return $settings->uptime_kuma_url;
            }
        } catch (\Exception $e) {
            Log::debug('IntegrationSettings not available, using config fallback', ['error' => $e->getMessage()]);
        }
        return config('cachet.uptime_kuma.url', 'http://localhost:3001');
    }

    /**
     * Resolve the status page slug from multiple sources.
     */
    protected function resolveStatusPageSlug(?string $override): string
    {
        if ($override !== null && $override !== '') {
            return $override;
        }
        try {
            $settings = app(IntegrationSettings::class);
            if ($settings->uptime_kuma_status_page_slug !== null && $settings->uptime_kuma_status_page_slug !== '') {
                return $settings->uptime_kuma_status_page_slug;
            }
        } catch (\Exception $e) {
            Log::debug('IntegrationSettings not available for slug, using config fallback');
        }
        return config('cachet.uptime_kuma.status_page_slug', 'default');
    }

    /**
     * Get the HTTP client instance.
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->acceptJson();
    }

    /**
     * Check if Uptime Kuma is reachable.
     */
    public function ping(): bool
    {
        try {
            $response = $this->client()->get('/api/entry-page');

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Uptime Kuma ping failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the status page slug.
     */
    public function getStatusPageSlug(): string
    {
        return $this->statusPageSlug;
    }

    /**
     * Fetch status page data including groups and monitors.
     * Returns the publicGroupList which contains groups with their monitors.
     *
     * @return array{config: array, publicGroupList: array, incident: ?array}|null
     */
    public function getStatusPageData(): ?array
    {
        try {
            $response = $this->client()->get("/api/status-page/{$this->statusPageSlug}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to fetch status page data', [
                'slug' => $this->statusPageSlug,
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch status page data from Uptime Kuma', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Fetch heartbeat data (current status) for all monitors on the status page.
     *
     * @return array{heartbeatList: array<int, array>, uptimeList: array}|null
     */
    public function getHeartbeatData(): ?array
    {
        try {
            $response = $this->client()->get("/api/status-page/heartbeat/{$this->statusPageSlug}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to fetch heartbeat data', [
                'slug' => $this->statusPageSlug,
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch heartbeat data from Uptime Kuma', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Get groups with monitors and their current status.
     *
     * @return array<int, array{id: int, name: string, weight: int, monitors: array}>
     */
    public function getGroupsWithMonitors(): array
    {
        // Fetch status page data
        $statusPageData = $this->getStatusPageData();

        if (! $statusPageData || empty($statusPageData['publicGroupList'])) {
            Log::info('No public groups found on status page', ['slug' => $this->statusPageSlug]);

            return [];
        }
        $heartbeatData = $this->getHeartbeatData();
        $heartbeatList = $heartbeatData['heartbeatList'] ?? [];
        $uptimeList = $heartbeatData['uptimeList'] ?? [];

        $groups = [];

        foreach ($statusPageData['publicGroupList'] as $group) {
            $monitors = [];

            foreach ($group['monitorList'] ?? [] as $monitor) {
                $monitorId = $monitor['id'];
                $status = 2; // Default to PENDING
                $recentHeartbeats = [];

                if (isset($heartbeatList[$monitorId]) && ! empty($heartbeatList[$monitorId])) {
                    $latestHeartbeat = end($heartbeatList[$monitorId]);
                    $status = (int) ($latestHeartbeat['status'] ?? 2);

                    //Store recent heartbeats for visualization (last 30 entries)
                    $allHeartbeats = $heartbeatList[$monitorId];
                    $recentHeartbeats = array_slice($allHeartbeats, -30);
                    $recentHeartbeats = array_map(fn ($hb) => [
                        'status' => (int) ($hb['status'] ?? 2),
                        'ping' => $hb['ping'] ?? null,
                        'time' => $hb['time'] ?? null,
                    ], $recentHeartbeats);
                }

                //certificate info
                $certExpiryDays = null;
                if (isset($monitor['certExpiryDaysRemaining'])) {
                    $certExpiryDays = $monitor['certExpiryDaysRemaining'];
                }

                $monitors[] = [
                    'id' => $monitorId,
                    'name' => $monitor['name'] ?? "Monitor {$monitorId}",
                    'type' => $monitor['type'] ?? 'http',
                    'url' => $monitor['url'] ?? null,
                    'status' => $status,
                    'uptime_24h' => $uptimeList["{$monitorId}_24"] ?? null,
                    'heartbeats' => $recentHeartbeats,
                    'certExpiryDaysRemaining' => $certExpiryDays,
                ];
            }

            $groups[] = [
                'id' => $group['id'],
                'name' => $group['name'] ?? 'Unnamed Group',
                'weight' => $group['weight'] ?? 0,
                'monitors' => $monitors,
            ];
        }

        Log::info('Fetched groups and monitors from Uptime Kuma', [
            'groups_count' => count($groups),
            'total_monitors' => array_sum(array_map(fn ($g) => count($g['monitors']), $groups)),
        ]);

        return $groups;
    }

    /**
     * Get a flat list of all monitors from all groups.
     *
     * @return array<int, array{id: int, name: string, type: string, url: ?string, status: int, group_id: int, group_name: string}>
     */
    public function getMonitors(): array
    {
        $groups = $this->getGroupsWithMonitors();
        $monitors = [];

        foreach ($groups as $group) {
            foreach ($group['monitors'] as $monitor) {
                $monitors[] = array_merge($monitor, [
                    'group_id' => $group['id'],
                    'group_name' => $group['name'],
                ]);
            }
        }

        return $monitors;
    }

    /**
     * Get a specific monitor's status.
     */
    public function getMonitorStatus(int $monitorId): ?array
    {
        $monitors = $this->getMonitors();

        foreach ($monitors as $monitor) {
            if ($monitor['id'] === $monitorId) {
                return $monitor;
            }
        }

        return null;
    }
}
