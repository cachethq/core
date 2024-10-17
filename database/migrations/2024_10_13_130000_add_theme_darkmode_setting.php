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
        rescue(fn () => $this->migrator->add('theme.light_background', 'rgba(249, 250, 251, 1)'));
        rescue(fn () => $this->migrator->add('theme.light_text', 'rgba(63, 63, 70, 1)'));
        rescue(fn () => $this->migrator->add('theme.dark_background', 'rgba(24, 24, 27, 1)'));
        rescue(fn () => $this->migrator->add('theme.dark_text', 'rgba(212, 212, 216, 1)'));
        rescue(fn () => $this->migrator->add('theme.font_family_sans', 'sans-serif'));
    }
};
