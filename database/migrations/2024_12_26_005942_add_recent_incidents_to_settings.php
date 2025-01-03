<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('app.recent_incidents_only', false);
        $this->migrator->add('app.recent_incidents_days', 7);
    }
};
