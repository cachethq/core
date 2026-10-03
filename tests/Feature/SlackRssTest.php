<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;

it('only includes public incidents in the slack feed', function () {
    $publicIncident = Incident::factory()->create([
        'name' => 'Public Incident',
        'visible' => ResourceVisibilityEnum::guest,
    ]);

    Update::factory()->forIncident($publicIncident)->create(['message' => 'Public update.']);

    $authenticatedIncident = Incident::factory()->create([
        'name' => 'Authenticated Incident',
        'visible' => ResourceVisibilityEnum::authenticated,
    ]);

    Update::factory()->forIncident($authenticatedIncident)->create(['message' => 'Authenticated update.']);

    $hiddenIncident = Incident::factory()->create([
        'name' => 'Hidden Incident',
        'visible' => ResourceVisibilityEnum::hidden,
    ]);

    Update::factory()->forIncident($hiddenIncident)->create(['message' => 'Hidden update.']);

    $embargoedIncident = Incident::factory()->create([
        'name' => 'Embargoed Incident',
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->addWeek(),
    ]);

    Update::factory()->forIncident($embargoedIncident)->create(['message' => 'Embargoed update.']);

    $this->get('/status/slack.rss')
        ->assertOk()
        ->assertSee('Public Incident')
        ->assertSee('Public update.')
        ->assertDontSee('Authenticated Incident')
        ->assertDontSee('Authenticated update.')
        ->assertDontSee('Hidden Incident')
        ->assertDontSee('Hidden update.')
        ->assertDontSee('Embargoed Incident')
        ->assertDontSee('Embargoed update.');
});

it('formats incident events for slack', function () {
    $reportedAt = Carbon::parse('2026-09-30 09:00:00');
    $identifiedAt = $reportedAt->copy()->addMinute();
    $resolvedAt = $identifiedAt->copy()->addMinute();

    $component = Component::factory()->create(['name' => 'API']);
    $incident = Incident::factory()->create([
        'name' => 'Elevated error rates',
        'message' => 'We are investigating.',
        'status' => IncidentStatusEnum::fixed,
        'visible' => ResourceVisibilityEnum::guest,
        'created_at' => $reportedAt,
        'updated_at' => $resolvedAt,
    ]);
    $incident->components()->attach($component);

    $identified = Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::identified,
        'message' => 'We identified the cause.',
        'created_at' => $identifiedAt,
        'updated_at' => $identifiedAt,
    ]);
    $resolved = Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::fixed,
        'message' => 'Service has recovered.',
        'created_at' => $resolvedAt,
        'updated_at' => $resolvedAt,
    ]);

    $response = $this->get('/status/slack.rss')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml');

    $xml = simplexml_load_string($response->getContent());

    expect($xml)->not->toBeFalse()
        ->and((string) $xml->channel->title)->toBe('Cachet incident updates')
        ->and((string) $xml->channel->description)->toBe('Latest incident reports and updates from Cachet')
        ->and($xml->channel->item)->toHaveCount(3)
        ->and((string) $xml->channel->item[0]->title)->toBe('[Fixed] Elevated error rates')
        ->and((string) $xml->channel->item[0]->guid)->toEndWith('#update-'.$resolved->id)
        ->and((string) $xml->channel->item[0]->description)
        ->toContain('<strong>Status:</strong> Fixed')
        ->toContain('Service has recovered.')
        ->toContain('<strong>Affected components:</strong> API')
        ->and((string) $xml->channel->item[1]->title)->toBe('[Identified] Elevated error rates')
        ->and((string) $xml->channel->item[1]->guid)->toEndWith('#update-'.$identified->id)
        ->and((string) $xml->channel->item[2]->title)->toBe('[Reported] Elevated error rates')
        ->and((string) $xml->channel->item[2]->guid)->toEndWith('#reported')
        ->and((string) $xml->channel->item[2]->description)
        ->toContain('<strong>Status:</strong> Reported')
        ->toContain('We are investigating.');

    $content = $xml->channel->item[0]->children('http://purl.org/rss/1.0/modules/content/');

    expect((string) $content->encoded)->toBe((string) $xml->channel->item[0]->description);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertSee('id="update-'.$resolved->id.'"', escape: false)
        ->assertSee('id="reported"', escape: false);
});

it('includes fresh updates to old incidents', function () {
    $incident = Incident::factory()->create([
        'name' => 'Long-running Incident',
        'visible' => ResourceVisibilityEnum::guest,
        'created_at' => now()->subYear(),
    ]);

    Update::factory()->forIncident($incident)->create([
        'message' => 'A fresh update.',
        'created_at' => now(),
    ]);

    Incident::factory()
        ->count(100)
        ->state(new Sequence(fn (Sequence $sequence): array => [
            'name' => 'Newer Incident '.$sequence->index,
            'visible' => ResourceVisibilityEnum::guest,
            'created_at' => now()->subDays($sequence->index + 1),
        ]))
        ->create();

    $this->get('/status/slack.rss')
        ->assertOk()
        ->assertSee('Long-running Incident')
        ->assertSee('A fresh update.');
});
