<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('shows the current status for a component with no linked incidents', function () {
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $view = $this->view('cachet::components.component', [
        'component' => $component,
        'status' => $component->status,
    ]);

    $view->assertSee('Operational');
});

it('shows the latest status for a component one linked incident', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ])
        ->incidents()
        ->attach(Incident::factory()->create(), [
            'component_status' => ComponentStatusEnum::performance_issues->value,
        ]);

    $component = Component::query()->withCount('incidents')->first();

    $view = $this->view('cachet::components.component', [
        'component' => $component,
        'status' => $component->status,
    ]);

    $view
        ->assertSee(ComponentStatusEnum::performance_issues->getLabel())
        ->assertDontSee(ComponentStatusEnum::operational->getLabel());
});

it('links the status pill to the latest unresolved incident, not an older resolved one', function () {
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $resolvedIncident = Incident::factory()->create([
        'status' => IncidentStatusEnum::fixed->value,
    ]);
    $ongoingIncident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating->value,
    ]);

    $this->travelTo(now()->subSeconds(2), function () use ($component, $resolvedIncident) {
        $component->incidents()->attach($resolvedIncident, [
            'component_status' => ComponentStatusEnum::performance_issues->value,
        ]);
    });

    $component->incidents()->attach($ongoingIncident, [
        'component_status' => ComponentStatusEnum::partial_outage->value,
    ]);

    $component = Component::query()
        ->withCount(['incidents' => fn ($query) => $query->unresolved()])
        ->first();

    $view = $this->view('cachet::components.component', [
        'component' => $component,
        'status' => $component->status,
    ]);

    $view
        ->assertSee($ongoingIncident->guid)
        ->assertDontSee($resolvedIncident->guid);
});

it('shows the latest status for a component multiple linked incidents', function () {
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $this->travelTo(now()->subSeconds(2), function () use ($component) {
        $component->incidents()->attach(Incident::factory()->create(), [
            'component_status' => ComponentStatusEnum::unknown->value,
        ]);
    });

    $this->travelTo(now()->subSeconds(1), function () use ($component) {
        $component->incidents()->attach(Incident::factory()->create(), [
            'component_status' => ComponentStatusEnum::performance_issues->value,
        ]);
    });

    $component->incidents()->attach(Incident::factory()->create(), [
        'component_status' => ComponentStatusEnum::partial_outage->value,
    ]);

    $component = Component::query()->withCount('incidents')->first();

    $view = $this->view('cachet::components.component', [
        'component' => $component,
        'status' => $component->status,
    ]);

    $view
        ->assertSee(ComponentStatusEnum::partial_outage->getLabel())
        ->assertDontSee(ComponentStatusEnum::unknown->getLabel())
        ->assertDontSee(ComponentStatusEnum::performance_issues->getLabel())
        ->assertDontSee(ComponentStatusEnum::operational->getLabel());
});
