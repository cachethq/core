<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('theme.dark_mode', 'system'));
        rescue(fn () => $this->migrator->add('theme.light_background', '#F9FAFB'));
        rescue(fn () => $this->migrator->add('theme.light_text', '#3F3F46'));
        rescue(fn () => $this->migrator->add('theme.dark_background', '#18181B'));
        rescue(fn () => $this->migrator->add('theme.dark_text', '#D4D4D8'));
    }
};
