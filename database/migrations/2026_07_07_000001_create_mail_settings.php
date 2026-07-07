<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('mail.allow_subscribers', false));
        rescue(fn () => $this->migrator->add('mail.notify_long_running_incidents', false));
        rescue(fn () => $this->migrator->add('mail.long_running_incident_hours', 6));
        rescue(fn () => $this->migrator->add('mail.mailer'));
        rescue(fn () => $this->migrator->add('mail.host'));
        rescue(fn () => $this->migrator->add('mail.port'));
        rescue(fn () => $this->migrator->addEncrypted('mail.username'));
        rescue(fn () => $this->migrator->addEncrypted('mail.password'));
        rescue(fn () => $this->migrator->add('mail.from_address'));
        rescue(fn () => $this->migrator->add('mail.from_name'));
    }
};
