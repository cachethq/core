<?php

use Cachet\Models\Incident;
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
