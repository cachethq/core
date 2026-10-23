<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        rescue(fn () => $this->migrator->add('captcha.provider', 'none'));
        rescue(fn () => $this->migrator->add('captcha.site_key'));
        rescue(fn () => $this->migrator->addEncrypted('captcha.secret_key'));
        rescue(fn () => $this->migrator->add('captcha.altcha_complexity', 50000));
    }
};
