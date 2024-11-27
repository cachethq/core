<?php

namespace Cachet\Actions\Integrations;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ExternalProviderEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Illuminate\Support\Carbon;

class ImportOhDearFeed
{
    /**
     * Import an OhDear feed.
     */
    public function __invoke(array $data, bool $importSites, ?int $componentGroupId, bool $importIncidents): void
    {
        if ($importSites) {
            $this->importSites($data['sites']['ungrouped'], $componentGroupId);
        }

        if ($importIncidents) {
            $this->importIncidents($data['updatesPerDay']);
        }
    }

    /**
     * Import OhDear sites as components.
     */
    private function importSites(array $sites, ?int $componentGroupId): void
    {
        foreach ($sites as $site) {
            Component::updateOrCreate(
                ['link' => $site['url']],
                [
                    'name' => $site['label'],
                    'component_group_id' => $componentGroupId,
                    'status' => $site['status'] === 'up' ? ComponentStatusEnum::operational : ComponentStatusEnum::partial_outage,
                ]
            );
        }
    }

    /**
     * Import OhDear incidents.
     */
    private function importIncidents(array $updatesPerDay): void
    {
        Incident::unguard();

        foreach ($updatesPerDay as $day => $incidents) {
            foreach ($incidents as $incident) {
                Incident::updateOrCreate(
                    [
                        'external_provider' => $provider = ExternalProviderEnum::OhDear,
                        'external_id' => $incident['id'],
                    ],
                    [
                        'name' => $incident['title'],
                        'status' => $provider->status($incident['severity']),
                        'message' => $incident['text'],
                        'occurred_at' => Carbon::createFromTimestamp($incident['time']),
                        'created_at' => Carbon::createFromTimestamp($incident['time']),
                    ]
                );
            }
        }
    }
}
