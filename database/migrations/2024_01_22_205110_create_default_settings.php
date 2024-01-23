\<?php

use Cachet\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If there are existing settings, we're upgrading from Cachet 2.x.
        if (Setting::count() > 0) {
            return;
        }

        // Cachet settings...
        $this->migrator->add('app.install_id', Str::random(40));
        $this->migrator->add('app.name', 'Cachet');
        $this->migrator->add('app.domain');
        $this->migrator->add('app.about');
        $this->migrator->add('app.timezone', 'UTC');
        $this->migrator->add('app.locale', 'en');
        $this->migrator->add('app.incident_days', 7);

        // Customization settings...
        $this->migrator->add('customization.header');
        $this->migrator->add('customization.footer');
        $this->migrator->add('customization.stylesheet');

        // Theme settings...
        $this->migrator->add('theme.banner_image');
    }
};
