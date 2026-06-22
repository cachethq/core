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
