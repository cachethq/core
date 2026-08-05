<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('app.show_site_name', false));
        rescue(fn () => $this->migrator->add('app.show_about', true));
    }
};
