<?php

namespace Cachet\Actions\UptimeKuma;

use Cachet\Data\UptimeKuma\UptimeKumaWebhookData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Cachet\Settings\IntegrationSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HandleUptimeKumaWebhook
{
    /**
     * Incident template slugs for automatic incident management.
     */
    protected const TEMPLATE_OUTAGE = 'service-outage';
    protected const TEMPLATE_RECOVERY = 'service-recovery';

    /**
     * Handle the Uptime Kuma webhook event.
     *
     * @return array{action: string, incident: ?Incident, component: ?Component}
     */
    public function handle(UptimeKumaWebhookData $data): array
    {
        if (! $data->isValid()) {
            return [
                'action' => 'ignored',
                'incident' => null,
                'component' => null,
                'reason' => 'Invalid webhook payload',
            ];
        }
        $component = $this->findComponentByMonitorId($data->getMonitorId());

        if (! $component) {
            return [
                'action' => 'ignored',
                'incident' => null,
                'component' => null,
                'reason' => 'No component linked to monitor ID: '.$data->getMonitorId(),
            ];
        }

        //Handle based on status
        if ($data->isDown()) {
            return $this->handleDownEvent($data, $component);
        }

        if ($data->isUp()) {
            return $this->handleUpEvent($data, $component);
        }

        if ($data->isMaintenance()) {
            return $this->handleMaintenanceEvent($data, $component);
        }
        return [
            'action' => 'ignored',
            'incident' => null,
            'component' => $component,
            'reason' => 'Pending status ignored',
        ];
    }

    /**
     * Find a component by its linked Uptime Kuma monitor ID.
     */
    protected function findComponentByMonitorId(int $monitorId): ?Component
    {
        return Component::query()
            ->where('meta->uptime_kuma_monitor_id', $monitorId)
            ->first();
    }

    /**
     * Handle a DOWN event from Uptime Kuma.
     */
    protected function handleDownEvent(UptimeKumaWebhookData $data, Component $component): array
    {
        // Check if there's already an active incident for this component from Uptime Kuma
        $existingIncident = $this->findActiveUptimeKumaIncident($component);

        if ($existingIncident) {
            return [
                'action' => 'already_exists',
                'incident' => $existingIncident,
                'component' => $component,
                'reason' => 'Active incident already exists',
            ];
        }
        $incident = $this->createIncident($data, $component);
        $component->update(['status' => ComponentStatusEnum::major_outage]);
        $this->updateHeartbeatMeta($component, $data);

        return [
            'action' => 'incident_created',
            'incident' => $incident,
            'component' => $component,
        ];
    }

    /**
     * Handle an UP event from Uptime Kuma.
     */
    protected function handleUpEvent(UptimeKumaWebhookData $data, Component $component): array
    {
        $incident = $this->findActiveUptimeKumaIncident($component);

        if (! $incident) {
            $component->update(['status' => ComponentStatusEnum::operational]);
        } else {
            //resolve the incident
            $this->resolveIncident($incident, $data);
            $component->update(['status' => ComponentStatusEnum::operational]);
        }

        $this->updateHeartbeatMeta($component, $data);

        return [
            'action' => $incident ? 'incident_resolved' : 'no_incident_to_resolve',
            'incident' => $incident ? $incident->fresh() : null,
            'component' => $component,
        ];
    }

    /**
     * Handle a MAINTENANCE event from Uptime Kuma.
     */
    protected function handleMaintenanceEvent(UptimeKumaWebhookData $data, Component $component): array
    {
        $component->update(['status' => ComponentStatusEnum::under_maintenance]);

        return [
            'action' => 'maintenance_status_set',
            'incident' => null,
            'component' => $component,
        ];
    }

    /**
     * Find an active incident created by Uptime Kuma for a component.
     */
    protected function findActiveUptimeKumaIncident(Component $component): ?Incident
    {
        return Incident::query()
            ->where('external_provider', 'uptime_kuma')
            ->whereIn('status', IncidentStatusEnum::unresolved())
            ->whereHas('components', fn ($query) => $query->where('components.id', $component->id))
            ->latest()
            ->first();
    }

    /**
     * Create a new incident from an Uptime Kuma DOWN event.
     */
    protected function createIncident(UptimeKumaWebhookData $data, Component $component): Incident
    {
        $monitorName = $data->getMonitorName() ?? 'Unknown Service';
        $message = $data->getHeartbeatMessage() ?? 'Service is not responding';
        $monitorUrl = $data->getMonitorUrl();
        $ping = $data->getPing();

        $occurredAt = $data->getHeartbeatTime()
            ? Carbon::parse($data->getHeartbeatTime())
            : Carbon::now();

        $template = IncidentTemplate::where('slug', self::TEMPLATE_OUTAGE)->first();

        if ($template) {
            $incidentMessage = $template->render([
                'service_name' => $monitorName,
                'component_name' => $component->name,
                'url' => $monitorUrl,
                'error_message' => $message,
                'ping' => $ping,
                'occurred_at' => $occurredAt->format('F j, Y \a\t g:i A T'),
                'date' => $occurredAt->format('F j, Y'),
                'time' => $occurredAt->format('g:i A T'),
            ]);
        } else {
            $incidentMessage = $this->generateDefaultOutageMessage($monitorName, $component, $message, $monitorUrl, $occurredAt, $ping);
        }

        $incident = Incident::create([
            'guid' => Str::uuid(),
            'external_provider' => 'uptime_kuma',
            'external_id' => (string) $data->getMonitorId(),
            'name' => "Service Disruption: {$component->name}",
            'status' => IncidentStatusEnum::investigating,
            'message' => $incidentMessage,
            'visible' => ResourceVisibilityEnum::guest,
            'stickied' => false,
            'notifications' => $this->shouldSendNotifications(),
            'occurred_at' => $occurredAt,
        ]);

        $incident->components()->attach($component->id, [
            'component_status' => ComponentStatusEnum::major_outage,
        ]);

        return $incident;
    }

    /**
     * Generate a professional default outage message.
     */
    protected function generateDefaultOutageMessage(
        string $serviceName,
        Component $component,
        string $errorMessage,
        ?string $url,
        Carbon $occurredAt,
        ?float $ping
    ): string {
        $message = "## Service Disruption Detected\n\n";
        $message .= "**Affected Service:** {$component->name}\n\n";
        $message .= "**Status:** Investigating\n\n";
        $message .= "**Detected:** {$occurredAt->format('F j, Y \a\t g:i A T')}\n\n";
        $message .= "---\n\n";
        $message .= "### Details\n\n";
        $message .= "Our monitoring systems have detected an issue with **{$serviceName}**.\n\n";

        if ($url) {
            $message .= "- **Endpoint:** `{$url}`\n";
        }

        $message .= "- **Error:** {$errorMessage}\n";

        if ($ping !== null) {
            $message .= "- **Last Response Time:** {$ping}ms\n";
        }

        $message .= "\n---\n\n";
        $message .= "### What's Next?\n\n";
        $message .= "Our team has been alerted and is actively investigating the issue. ";
        $message .= "We will provide updates as more information becomes available.\n\n";
        $message .= "_This incident was automatically detected by our monitoring system._";

        return $message;
    }

    /**
     * Resolve an existing incident when service comes back up.
     */
    protected function resolveIncident(Incident $incident, UptimeKumaWebhookData $data): void
    {
        $resolvedAt = $data->getHeartbeatTime()
            ? Carbon::parse($data->getHeartbeatTime())
            : Carbon::now();
        $duration = $incident->occurred_at
            ? $incident->occurred_at->diffForHumans($resolvedAt, ['syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])
            : 'unknown duration';

        $template = IncidentTemplate::where('slug', self::TEMPLATE_RECOVERY)->first();

        if ($template) {
            $updateMessage = $template->render([
                'service_name' => $data->getMonitorName() ?? 'Service',
                'resolved_at' => $resolvedAt->format('F j, Y \a\t g:i A T'),
                'duration' => $duration,
                'date' => $resolvedAt->format('F j, Y'),
                'time' => $resolvedAt->format('g:i A T'),
            ]);
        } else {
            $updateMessage = $this->generateDefaultRecoveryMessage($resolvedAt, $duration);
        }
        $incident->updates()->create([
            'status' => IncidentStatusEnum::fixed,
            'message' => $updateMessage,
        ]);
        $incident->update([
            'status' => IncidentStatusEnum::fixed,
        ]);
        $incident->components()->updateExistingPivot(
            $incident->components->pluck('id')->toArray(),
            ['component_status' => ComponentStatusEnum::operational]
        );
    }

    /**
     * Generate a professional default recovery message.
     */
    protected function generateDefaultRecoveryMessage(Carbon $resolvedAt, string $duration): string
    {
        $message = "## Service Restored\n\n";
        $message .= "**Status:** Resolved\n\n";
        $message .= "**Resolved:** {$resolvedAt->format('F j, Y \a\t g:i A T')}\n\n";
        $message .= "**Total Downtime:** {$duration}\n\n";
        $message .= "---\n\n";
        $message .= "The service has been restored and is now operating normally. ";
        $message .= "We apologize for any inconvenience this may have caused.\n\n";
        $message .= "If you continue to experience issues, please contact our support team.\n\n";
        $message .= "_This update was automatically generated by our monitoring system._";

        return $message;
    }

    /**
     * Check if notifications should be sent.
     * Uses database settings with fallback to config.
     */
    protected function shouldSendNotifications(): bool
    {
        try {
            $settings = app(IntegrationSettings::class);

            return $settings->uptime_kuma_send_notifications;
        } catch (\Exception $e) {
            Log::debug('IntegrationSettings not available for notifications check');

            return config('cachet.uptime_kuma.send_notifications', true);
        }
    }

    /**
     * Update component meta with heartbeat data from webhook event.
     * This ensures response time and last check info are always current.
     */
    protected function updateHeartbeatMeta(Component $component, UptimeKumaWebhookData $data): void
    {
        $meta = $component->meta ?? [];

        if ($data->getHeartbeatTime()) {
            $meta['uptime_kuma_last_heartbeat'] = $data->getHeartbeatTime();
        }

        if ($data->getPing() !== null) {
            $meta['uptime_kuma_last_ping'] = $data->getPing();
        }

        // Append to recent heartbeat history (last 30)
        $heartbeats = $meta['heartbeats'] ?? [];
        $heartbeats[] = [
            'status' => $data->getStatus() === UptimeKumaWebhookData::STATUS_UP ? 1 : 0,
            'ping' => $data->getPing(),
            'time' => $data->getHeartbeatTime() ?? now()->toIso8601String(),
        ];
        $meta['heartbeats'] = array_slice($heartbeats, -30);

        $meta['uptime_kuma_last_sync'] = now()->toIso8601String();

        $component->update(['meta' => $meta]);
    }
}
