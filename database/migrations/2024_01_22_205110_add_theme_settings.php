<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Theme settings...
        rescue(fn () => $this->migrator->add('theme.accent', 'cachet'));
        rescue(fn () => $this->migrator->add('theme.accent_content', 'zinc'));
        rescue(fn () => $this->migrator->add('theme.accent_pairing', true));
    }
};
