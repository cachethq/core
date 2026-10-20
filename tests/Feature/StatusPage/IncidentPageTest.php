<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Workbench\App\User;

it('never exposes a public incident private note on the status page', function (bool $authenticated) {
    if ($authenticated) {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'message' => 'Public incident message',
        'private_note' => 'Internal ticket SEC-482',
    ]);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertSee('Public incident message')
        ->assertDontSee('Internal ticket SEC-482');
})->with([false, true]);

it('never exposes a public incident private note to guest API viewers', function () {
    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'message' => 'Public incident message',
        'private_note' => 'Internal ticket SEC-482',
    ]);

    $this->getJson('/status/api/incidents/'.$incident->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.message', 'Public incident message')
        ->assertJsonMissingPath('data.attributes.private_note')
        ->assertDontSee('Internal ticket SEC-482');
});

it('exposes a private note to a first-party session on the API', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));

    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'private_note' => 'Internal ticket SEC-482',
    ]);

    $this->getJson('/status/api/incidents/'.$incident->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.private_note', 'Internal ticket SEC-482');
});

it('shows the affected components on the incident page', function () {
    $component = Component::factory()->create(['name' => 'API']);
    $incident = Incident::factory()->create();
    $incident->components()->attach($component, [
        'component_status' => ComponentStatusEnum::performance_issues->value,
    ]);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertSee(__('cachet::incident.affected_components_header'))
        ->assertSee('API');
});

it('does not show the affected components box when none are attached', function () {
    $incident = Incident::factory()->create();

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertDontSee(__('cachet::incident.affected_components_header'));
});

it('does not render raw html in incident messages', function () {
    $incident = Incident::factory()->create([
        'message' => 'We are **investigating**. <script>alert(1)</script>',
    ]);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertSee('<strong>investigating</strong>', escape: false)
        ->assertDontSee('<script>alert(1)</script>', escape: false);
});

it('uses the incident name as the page heading', function () {
    $incident = Incident::factory()->create(['name' => 'API connectivity']);

    $page = $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->getContent();

    expect($page)->toMatch('/<h1[^>]*>\s*API connectivity\s*<\/h1>/');
});

it('renders stable incident page attributes', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->create();
    $incident->components()->attach($component, [
        'component_status' => ComponentStatusEnum::performance_issues->value,
    ]);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertOk()
        ->assertSee('data-page="incident"', escape: false)
        ->assertSee('data-component="affected-components"', escape: false)
        ->assertSee('data-component="incident"', escape: false)
        ->assertSee('data-component="incident-update"', escape: false)
        ->assertSee('data-component="incident-update-status"', escape: false)
        ->assertSee('data-component="badge"', escape: false)
        ->assertSee('data-component="timestamp"', escape: false)
        ->assertSee('data-component="page-navigation"', escape: false)
        ->assertSee('data-slot="main"', escape: false)
        ->assertSee('data-slot="indicator"', escape: false)
        ->assertSee('data-slot="message"', escape: false);
});
