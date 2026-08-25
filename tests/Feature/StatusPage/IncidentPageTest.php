<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;

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
