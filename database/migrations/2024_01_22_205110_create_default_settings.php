<?php

use Illuminate\Support\Str;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cachet settings...
        rescue(fn () => $this->migrator->add('app.install_id', Str::random(40)));
        rescue(fn () => $this->migrator->add('app.name', 'Cachet'));
        rescue(fn () => $this->migrator->add('app.domain'));
        rescue(fn () => $this->migrator->add('app.about'));
        rescue(fn () => $this->migrator->add('app.timezone', 'UTC'));
        rescue(fn () => $this->migrator->add('app.locale', 'en'));
        rescue(fn () => $this->migrator->add('app.incident_days', 7));
        rescue(fn () => $this->migrator->add('app.refresh_rate'));
        rescue(fn () => $this->migrator->add('app.display_graphs', true));
        rescue(fn () => $this->migrator->add('app.show_support', true));
        rescue(fn () => $this->migrator->add('app.show_timezone', false));
        rescue(fn () => $this->migrator->add('app.only_disrupted_days', false));
        rescue(fn () => $this->migrator->add('app.dashboard_login_link', true));

        // Customization settings...
        rescue(fn () => $this->migrator->add('customization.header'));
        rescue(fn () => $this->migrator->add('customization.footer'));
        rescue(fn () => $this->migrator->add('customization.stylesheet'));

        // Theme settings...
        rescue(fn () => $this->migrator->add('theme.app_banner'));
    }
};
