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
        rescue(fn () => $this->migrator->add('app.api_enabled', true));
        rescue(fn () => $this->migrator->add('app.api_protected', false));
    }
    /**
     * Run the migrations.
     */
    public function down(): void
    {
        // Theme settings...
        rescue(fn () => $this->migrator->deleteIfExists('app.api_enabled'));
        rescue(fn () => $this->migrator->deleteIfExists('app.api_protected'));
    }
};
