<?php

namespace Cachet\Actions\Integrations;

use Cachet\Actions\Component\ChangeComponentStatus;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Enums\ExternalProviderEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Illuminate\Support\Carbon;

class ImportOhDearFeed
{
    public function __construct(private ChangeComponentStatus $changeComponentStatus)
    {
        //
    }

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
            $status = ExternalProviderEnum::OhDear->componentStatus($site['status']);
            $attributes = ['name' => $site['label'], 'component_group_id' => $componentGroupId];

            $component = Component::firstOrNew(['link' => $site['url']]);

            if (! $component->exists) {
                $component->fill([...$attributes, 'status' => $status])->save();

                continue;
            }

            $this->changeComponentStatus->handle(
                $component,
                $status,
                ComponentStatusSourceEnum::Import,
                reason: ExternalProviderEnum::OhDear->value,
                attributes: $attributes,
            );
        }
    }

    /**
     * Import OhDear incidents.
     */
    private function importIncidents(array $updatesPerDay): void
    {
        Incident::unguarded(function () use ($updatesPerDay): void {
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
        });
    }
}
