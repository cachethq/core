<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Integration settings for external monitoring systems.
 *
 * These settings are stored in the database and can be changed at runtime
 * through the admin dashboard without requiring .env changes.
 */
class IntegrationSettings extends Settings
{
    /**
     * Uptime Kuma server URL (e.g., http://localhost:3001)
     */
    public ?string $uptime_kuma_url = null;

    /**
     * Uptime Kuma status page slug (e.g., 'united-codes')
     */
    public ?string $uptime_kuma_status_page_slug = null;

    /**
     * Whether Uptime Kuma integration is enabled
     */
    public bool $uptime_kuma_enabled = true;

    /**
     * Whether to automatically create incidents when monitors go down
     */
    public bool $uptime_kuma_auto_incidents = true;

    /**
     * Whether to automatically resolve incidents when monitors come back up
     */
    public bool $uptime_kuma_auto_resolve = true;

    /**
     * Whether to send notifications to subscribers for Uptime Kuma incidents
     */
    public bool $uptime_kuma_send_notifications = true;

    /**
     * Auto-sync interval in minutes (0 = disabled)
     */
    public int $uptime_kuma_sync_interval = 5;

    /**
     * Last sync timestamp
     */
    public ?string $uptime_kuma_last_sync = null;

    /**
     * Webhook secret for authenticating Uptime Kuma webhooks
     */
    public ?string $uptime_kuma_webhook_secret = null;

    public static function group(): string
    {
        return 'integrations';
    }

    /**
     * Get the effective Uptime Kuma URL.
     * Falls back to config/env if not set in settings.
     */
    public function getUptimeKumaUrl(): string
    {
        return $this->uptime_kuma_url
            ?? config('cachet.uptime_kuma.url');
    }

    /**
     * Get the effective status page slug.
     * Falls back to config/env if not set in settings.
     */
    public function getUptimeKumaStatusPageSlug(): string
    {
        return $this->uptime_kuma_status_page_slug
            ?? config('cachet.uptime_kuma.status_page_slug')
            ?? 'default';
    }

    /**
     * Check if Uptime Kuma integration is enabled.
     */
    public function isUptimeKumaEnabled(): bool
    {
        return $this->uptime_kuma_enabled
            && config('cachet.uptime_kuma.enabled', true);
    }

    /**
     * Get the effective webhook secret.
     * Checks database first, then falls back to config/env.
     */
    public function getWebhookSecret(): ?string
    {
        return $this->uptime_kuma_webhook_secret
            ?? config('cachet.uptime_kuma.webhook_secret');
    }

    /**
     * Generate a new random webhook secret.
     */
    public function generateWebhookSecret(): string
    {
        $secret = bin2hex(random_bytes(32));
        $this->uptime_kuma_webhook_secret = $secret;
        $this->save();

        return $secret;
    }
}
