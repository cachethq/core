<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('app.api_enabled', true));
        rescue(fn () => $this->migrator->add('app.api_protected', false));
    }
};
