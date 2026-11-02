<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            'incident_unknown',
            'incident_investigating',
            'incident_identified',
            'incident_watching',
            'incident_fixed',
            'maintenance_upcoming',
            'maintenance_in_progress',
            'maintenance_complete',
        ] as $property) {
            rescue(fn () => $this->migrator->add('rss.'.$property, true));
        }

        rescue(fn () => $this->migrator->add('rss.max_items', 100));
        rescue(fn () => $this->migrator->add('rss.refresh_interval', 60));
    }
};
