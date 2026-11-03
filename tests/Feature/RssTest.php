<?php

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Support\Carbon;

it('can get the rss feed', function () {
    $incident = Incident::factory()->create();

    $this->get('/status/rss')
        ->assertOk()
        ->assertSee($incident->name);
});

it('returns cache validators for the rss feed', function () {
    Incident::factory()->create();

    $response = $this->get('/status/rss')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertHeader('Cache-Control', 'max-age=60, public')
        ->assertHeader('ETag')
        ->assertHeader('Last-Modified');

    $this->withHeaders(['If-None-Match' => $response->headers->get('ETag')])
        ->get('/status/rss')
        ->assertNotModified();
});

it('updates an incident item when an incident update is added', function () {
    $incident = Incident::factory()->create([
        'message' => 'We are **investigating** the issue.',
    ]);

    $initialFeed = simplexml_load_string($this->get('/status/rss')->assertOk()->getContent());
    $initialGuid = (string) $initialFeed->channel->item[0]->guid;

    $updatedAt = Carbon::now()->addMinute();

    Update::factory()->forIncident($incident)->create([
        'message' => 'We [identified](https://example.com) the affected service.',
        'created_at' => $updatedAt,
        'updated_at' => $updatedAt,
    ]);

    $updatedFeed = simplexml_load_string($this->get('/status/rss')->assertOk()->getContent());
    $item = $updatedFeed->channel->item[0];

    expect((string) $item->description)
        ->toContain('<strong>investigating</strong>')
        ->toContain('<a href="https://example.com">identified</a> the affected service.')
        ->and((string) $item->guid)->not->toBe($initialGuid)
        ->and((string) $item->guid['isPermaLink'])->toBe('false')
        ->and((string) $item->pubDate)->toBe($updatedAt->toRssString());
});

it('produces valid xml when an incident message contains a cdata terminator', function () {
    $incident = Incident::factory()->create([
        'message' => 'Something broke: ]]><script>alert(1)</script>',
    ]);

    Update::factory()->forIncident($incident)->create([
        'message' => 'Still broken: ]]><script>alert(2)</script>',
    ]);

    $response = $this->get('/status/rss')->assertOk();

    $xml = simplexml_load_string($response->getContent());

    expect($xml)->not->toBeFalse()
        ->and((string) $xml->channel->item[0]->description)
        ->toContain(']]&gt;alert(1)')
        ->toContain(']]&gt;alert(2)');
});

it('includes incidents and maintenance in the combined feed', function () {
    $incident = Incident::factory()->create();
    $schedule = Schedule::factory()->create();

    $this->get('/status/rss')
        ->assertOk()
        ->assertSee($incident->name)
        ->assertSee($schedule->name);
});

it('limits the incidents feed to incidents', function () {
    $incident = Incident::factory()->create();
    $schedule = Schedule::factory()->create();

    $this->get('/status/rss/incidents')
        ->assertOk()
        ->assertSee($incident->name)
        ->assertDontSee($schedule->name);
});

it('limits the maintenance feed to maintenance', function () {
    $incident = Incident::factory()->create();
    $schedule = Schedule::factory()->create();

    $this->get('/status/rss/maintenance')
        ->assertOk()
        ->assertSee($schedule->name)
        ->assertDontSee($incident->name);
});

it('filters feeds by components', function () {
    $component = Component::factory()->create();
    $matching = Incident::factory()->create();
    $matching->components()->attach($component);
    $other = Incident::factory()->create();

    $this->get('/status/rss/incidents?components='.$component->id)
        ->assertOk()
        ->assertSee($matching->name)
        ->assertDontSee($other->name);

    $this->get('/status/rss/incidents?components[]='.$component->id)
        ->assertOk()
        ->assertSee($matching->name)
        ->assertDontSee($other->name);
});

it('lists affected components on each item', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->create();
    $incident->components()->attach($component);

    $item = simplexml_load_string($this->get('/status/rss')->assertOk()->getContent())->channel->item[0];

    expect((string) $item->category)->toBe($component->name)
        ->and((string) $item->category['domain'])->toBe('component:'.$component->id);
});

it('does not expose disabled or non-public components', function () {
    $public = Component::factory()->create();
    $disabled = Component::factory()->create(['name' => 'Disabled part zq3', 'enabled' => false]);
    $privateGroup = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    $grouped = Component::factory()->create(['name' => 'Grouped part zq4', 'component_group_id' => $privateGroup->id]);

    $incident = Incident::factory()->create();
    $incident->components()->attach([$public->id, $disabled->id, $grouped->id]);
    $schedule = Schedule::factory()->create();
    $schedule->components()->attach([$disabled->id, $grouped->id], ['component_status' => 1]);

    $this->get('/status/rss')
        ->assertOk()
        ->assertSee($incident->name)
        ->assertSee($public->name)
        ->assertDontSee($disabled->name)
        ->assertDontSee($grouped->name);

    foreach ([$disabled, $grouped] as $hidden) {
        $this->get('/status/rss?components='.$hidden->id)
            ->assertOk()
            ->assertDontSee($incident->name)
            ->assertDontSee($schedule->name);
    }
});

it('hides unpublished and non-public incidents and maintenance', function () {
    $unpublished = Incident::factory()->create(['published_at' => now()->addDay()]);
    $private = Incident::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    $futureSchedule = Schedule::factory()->create(['published_at' => now()->addDay()]);

    $this->get('/status/rss')
        ->assertOk()
        ->assertDontSee($unpublished->name)
        ->assertDontSee($private->name)
        ->assertDontSee($futureSchedule->name);
});
