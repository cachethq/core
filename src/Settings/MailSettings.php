<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    public bool $allow_subscribers = false;

    public bool $notify_long_running_incidents = false;

    public int $long_running_incident_hours = 6;

    public ?string $mailer = null;

    public ?string $host = null;

    public ?int $port = null;

    public ?string $username = null;

    public ?string $password = null;

    public ?string $from_address = null;

    public ?string $from_name = null;

    public static function group(): string
    {
        return 'mail';
    }

    /**
     * @return list<string>
     */
    public static function encrypted(): array
    {
        return ['username', 'password'];
    }

    /**
     * Determine whether Cachet's mail settings should override the application's mail configuration.
     */
    public function configured(): bool
    {
        return $this->mailer !== null;
    }

    /**
     * Build the Laravel mailer configuration for these settings.
     *
     * @return array<string, mixed>
     */
    public function toMailerConfig(): array
    {
        return match ($this->mailer) {
            'smtp' => [
                'transport' => 'smtp',
                'host' => $this->host,
                'port' => $this->port ?? 587,
                'username' => $this->username,
                'password' => $this->password,
                'timeout' => null,
            ],
            'sendmail' => [
                'transport' => 'sendmail',
                'path' => config('mail.mailers.sendmail.path', '/usr/sbin/sendmail -bs'),
            ],
            default => [
                'transport' => $this->mailer,
            ],
        };
    }
}
