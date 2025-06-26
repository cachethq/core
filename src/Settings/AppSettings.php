<?php

namespace Cachet\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $install_id;

    public ?string $name = 'Cachet';

    public ?string $about;

    public bool $show_support = true;

    public string $locale = 'en';

    public string $timezone = 'UTC';

    public bool $show_timezone = false;

    public bool $only_disrupted_days = false;

    public int $incident_days = 7;

    public ?int $refresh_rate;

    public bool $dashboard_login_link = true;

    public int $major_outage_threshold = 25;

    public bool $recent_incidents_only = false;

    public int $recent_incidents_days = 7;

    public bool $display_graphs = true;

    public bool $enable_external_dependencies = true;

    public static function group(): string
    {
        return 'app';
    }
}
