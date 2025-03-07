<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Exceptions\MissingSettings;
use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $install_id;

    public ?string $name = 'Cachet';

    public ?string $about;

    public bool $show_support = true;

    public string $timezone = 'UTC';

    public bool $show_timezone = false;

    public bool $only_disrupted_days = false;

    public int $incident_days = 7;

    public ?int $refresh_rate;

    public bool $dashboard_login_link = true;

    public int $major_outage_threshold = 25;

    public bool $recent_incidents_only = false;

    public int $recent_incidents_days = 7;

    public bool $api_enabled = true;

    public bool $api_protected = false;

    public static function group(): string
    {
        return 'app';
    }


    public static function getOrDefault(string $name, mixed $default = null): mixed
    {
        try {
            return app(self::class)->$name;
        } catch (MissingSettings $exception) {
            if(! app()->runningInConsole()) {
                throw new \Exception("Please run `php artisan migrate` to load missing settings.");
            }
        }
        return $default;
    }
}
