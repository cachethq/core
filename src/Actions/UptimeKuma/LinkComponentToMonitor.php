<?php

namespace Cachet\Actions\UptimeKuma;

use Cachet\Models\Component;

class LinkComponentToMonitor
{
    /**
     * Link a Cachet component to an Uptime Kuma monitor ID.
     */
    public function handle(Component $component, int $monitorId): Component
    {
        $meta = $component->meta ?? [];
        $meta['uptime_kuma_monitor_id'] = $monitorId;

        $component->update(['meta' => $meta]);

        return $component->fresh();
    }

    /**
     * Unlink a component from its Uptime Kuma monitor.
     */
    public function unlink(Component $component): Component
    {
        $meta = $component->meta ?? [];
        unset($meta['uptime_kuma_monitor_id']);

        $component->update(['meta' => $meta]);

        return $component->fresh();
    }

    /**
     * Get the Uptime Kuma monitor ID for a component.
     */
    public function getMonitorId(Component $component): ?int
    {
        return $component->meta['uptime_kuma_monitor_id'] ?? null;
    }
}
