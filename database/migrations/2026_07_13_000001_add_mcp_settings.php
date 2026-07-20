<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('app.mcp_enabled', false));
        rescue(fn () => $this->migrator->add('app.mcp_protected', true));
    }
};
