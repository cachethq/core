<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('integrations.uptime_kuma_webhook_secret', null);
    }

    public function down(): void
    {
        $this->migrator->delete('integrations.uptime_kuma_webhook_secret');
    }
};
