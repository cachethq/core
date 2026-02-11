<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('integrations.uptime_kuma_url', null);
        $this->migrator->add('integrations.uptime_kuma_status_page_slug', null);
        $this->migrator->add('integrations.uptime_kuma_enabled', true);
        $this->migrator->add('integrations.uptime_kuma_auto_incidents', true);
        $this->migrator->add('integrations.uptime_kuma_auto_resolve', true);
        $this->migrator->add('integrations.uptime_kuma_send_notifications', true);
        $this->migrator->add('integrations.uptime_kuma_sync_interval', 5);
        $this->migrator->add('integrations.uptime_kuma_last_sync', null);
    }

    public function down(): void
    {
        $this->migrator->delete('integrations.uptime_kuma_url');
        $this->migrator->delete('integrations.uptime_kuma_status_page_slug');
        $this->migrator->delete('integrations.uptime_kuma_enabled');
        $this->migrator->delete('integrations.uptime_kuma_auto_incidents');
        $this->migrator->delete('integrations.uptime_kuma_auto_resolve');
        $this->migrator->delete('integrations.uptime_kuma_send_notifications');
        $this->migrator->delete('integrations.uptime_kuma_sync_interval');
        $this->migrator->delete('integrations.uptime_kuma_last_sync');
    }
};
