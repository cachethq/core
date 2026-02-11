<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\UptimeKuma\HandleUptimeKumaWebhook;
use Cachet\Data\UptimeKuma\UptimeKumaWebhookData;
use Cachet\Settings\IntegrationSettings;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

#[Group('Integrations', weight: 10)]
class UptimeKumaWebhookController extends Controller
{
    public function __construct(
        protected HandleUptimeKumaWebhook $handler
    ) {}

    /**
     * Receive Uptime Kuma Webhook
     *
     * Receives webhook notifications from Uptime Kuma monitoring system.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Verify the webhook secret if configured
        if (! $this->verifyWebhookSecret($request)) {
            Log::warning('Uptime Kuma webhook received with invalid secret', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid webhook secret',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $data = UptimeKumaWebhookData::from($request->all());
        Log::info('Uptime Kuma webhook received', [
            'monitor_id' => $data->getMonitorId(),
            'monitor_name' => $data->getMonitorName(),
            'status' => $data->getStatus(),
            'message' => $data->getHeartbeatMessage(),
        ]);
        $result = $this->handler->handle($data);
        Log::info('Uptime Kuma webhook processed', [
            'action' => $result['action'],
            'incident_id' => $result['incident']?->id,
            'component_id' => $result['component']?->id,
        ]);

        return response()->json([
            'success' => true,
            'action' => $result['action'],
            'incident_id' => $result['incident']?->id,
            'component_id' => $result['component']?->id,
            'reason' => $result['reason'] ?? null,
        ]);
    }

    /**
     * Verify the webhook secret if one is configured.
     */
    protected function verifyWebhookSecret(Request $request): bool
    {
        // Check database settings first, then fall back to config/env
        $settings = app(IntegrationSettings::class);
        $configuredSecret = $settings->getWebhookSecret();
        if (empty($configuredSecret)) {
            return true;
        }

        // Check the Authorization header
        $providedSecret = $request->header('X-Webhook-Secret')
            ?? $request->header('Authorization')
            ?? $request->query('secret');
        if (str_starts_with($providedSecret ?? '', 'Bearer ')) {
            $providedSecret = substr($providedSecret, 7);
        }

        return hash_equals($configuredSecret, $providedSecret ?? '');
    }
}
