<?php

namespace Cachet\Settings;

use Cachet\Settings\Attributes\Description;
use Illuminate\Support\Arr;
use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    #[Description('The unique install ID of the application for telemetry.')]
    public string $install_id;

    #[Description('What is the name of your application?')]
    public ?string $name = 'Cachet';

    #[Description('What is your application about?', required: false)]
    public ?string $about;

    #[Description('Do you want to show support for Cachet?')]
    public bool $show_support = true;

    #[Description('What timezone is is the application located in?')]
    public string $timezone = 'UTC';

    #[Description('Would you like to show your timezone on the status page?')]
    public bool $show_timezone = false;

    #[Description('Would you like to only show the days with disruption?')]
    public bool $only_disrupted_days = false;

    #[Description('How many incident days should be shown in the timeline?')]
    public int $incident_days = 7;

    #[Description('After how many seconds should the status page automatically refresh?', required: false)]
    public ?int $refresh_rate;

    #[Description('Should the dashboard login link be shown?')]
    public bool $dashboard_login_link = true;

    #[Description('Major outage threshold %')]
    public int $major_outage_threshold = 25;

    public static function group(): string
    {
        return 'app';
    }

    public function installable(): array
    {
        return Arr::except(get_class_vars(__CLASS__), [
            'install_id',
        ]);
    }
}
