<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;

it('renders incident details with stable theme selectors', function () {
    $component = Component::factory()->create(['name' => 'API']);
    $incident = Incident::factory()->create(['name' => 'API connectivity']);
    $incident->components()->attach($component, [
        'component_status' => ComponentStatusEnum::performance_issues->value,
    ]);

    $this->get(route('cachet.status-page.incident', $incident))
        ->assertSeeText('API connectivity')
        ->assertSee(__('cachet::incident.affected_components_header'))
        ->assertSee('API')
        ->assertSee([
            'data-page="incident"',
            'data-component="affected-components"',
            'data-component="incident"',
            'data-component="incident-update"',
            'data-component="incident-update-status"',
            'data-component="badge"',
            'data-component="timestamp"',
            'data-component="page-navigation"',
            'data-slot="main"',
            'data-slot="indicator"',
            'data-slot="message"',
        ], escape: false);
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
        ->assertSeeText('investigating')
        ->assertDontSee('<script>alert(1)</script>', escape: false);
});
