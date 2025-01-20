<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\View\Components\Component as ComponentView;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('shows the current status for a component with no linked incidents', function () {
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value
    ]);

    $view = $this->view('cachet::components.component', ['component' => $component, 'status' => $component->status]);

    $view->assertSee('Operational');
});

it('shows the latest status for a component with linked incidents', function () {
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);
    $incident = Incident::factory()->create();

    $incident->components()->attach($component, ['component_status' => ComponentStatusEnum::performance_issues->value]);

    $view = $this->view('cachet::components.component', ['component' => $component, 'status' => $component->status]);

    $view
        ->assertSee(ComponentStatusEnum::performance_issues->getLabel())
        ->assertDontSee(ComponentStatusEnum::operational->getLabel());
})->todo();
