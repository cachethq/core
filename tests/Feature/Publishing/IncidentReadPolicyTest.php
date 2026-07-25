<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Status;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

it('keeps embargoed incidents out of the rss feed', function () {
    Incident::factory()->create([
        'name' => 'Embargoed Incident',
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->addWeek(),
    ]);

    $this->get(route('cachet.rss'))
        ->assertOk()
        ->assertDontSee('Embargoed Incident');
});

it('keeps hidden incidents out of the rss feed', function () {
    Incident::factory()->create([
        'name' => 'Hidden Incident',
        'visible' => ResourceVisibilityEnum::hidden,
    ]);

    $this->get(route('cachet.rss'))
        ->assertOk()
        ->assertDontSee('Hidden Incident');
});

it('hides an incident page from guests when it is not visible to them', function (ResourceVisibilityEnum $visibility) {
    $incident = Incident::factory()->create(['visible' => $visibility]);

    $this->get(route('cachet.status-page.incident', $incident))->assertNotFound();
})->with([
    'hidden' => [ResourceVisibilityEnum::hidden],
    'authenticated only' => [ResourceVisibilityEnum::authenticated],
]);

it('keeps an incident nobody can see out of the system status', function (array $attributes) {
    Component::factory()->create(['status' => ComponentStatusEnum::operational, 'enabled' => true]);

    Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        ...$attributes,
    ]);

    expect((new Status)->incidents()->unresolved)->toBe(0);
})->with([
    'embargoed' => [['visible' => ResourceVisibilityEnum::guest, 'published_at' => now()->addWeek()]],
    'hidden' => [['visible' => ResourceVisibilityEnum::hidden]],
]);

it('hides the updates of an embargoed incident from the api', function () {
    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->addWeek(),
    ]);

    $incident->updates()->create(['status' => IncidentStatusEnum::investigating, 'message' => 'Looking into it.']);

    $this->getJson('/status/api/incidents/'.$incident->id.'/updates')->assertNotFound();
});

it('exposes the updates of an embargoed incident to a token that can manage incidents', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'published_at' => now()->addWeek(),
    ]);

    $incident->updates()->create(['status' => IncidentStatusEnum::investigating, 'message' => 'Looking into it.']);

    $this->getJson('/status/api/incidents/'.$incident->id.'/updates')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});
