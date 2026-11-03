<?php

namespace Cachet\Settings;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Spatie\LaravelSettings\Settings;

class RssSettings extends Settings
{
    public bool $incident_unknown = true;

    public bool $incident_investigating = true;

    public bool $incident_identified = true;

    public bool $incident_watching = true;

    public bool $incident_fixed = true;

    public bool $maintenance_upcoming = true;

    public bool $maintenance_in_progress = true;

    public bool $maintenance_complete = true;

    /** Maximum items of each type in a feed. */
    public int $max_items = 100;

    /** Seconds a feed is cached for and clients are told to wait before refetching. */
    public int $refresh_interval = 60;

    public static function group(): string
    {
        return 'rss';
    }

    /**
     * Incident statuses included in the feeds.
     *
     * @return list<IncidentStatusEnum>
     */
    public function incidentStatuses(): array
    {
        return array_values(array_filter(
            IncidentStatusEnum::cases(),
            fn (IncidentStatusEnum $status): bool => $this->{'incident_'.$status->name},
        ));
    }

    /**
     * Maintenance statuses included in the feeds.
     *
     * @return list<ScheduleStatusEnum>
     */
    public function maintenanceStatuses(): array
    {
        return array_values(array_filter(
            ScheduleStatusEnum::cases(),
            fn (ScheduleStatusEnum $status): bool => $this->{'maintenance_'.$status->name},
        ));
    }
}
