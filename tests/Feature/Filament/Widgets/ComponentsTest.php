<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Widgets\Components;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

it('component smoke test', function () {
    $component = livewire(Components::class);

    $component->assertSuccessful();
});

it('will only show visible component groups', function () {
    ComponentGroup::factory()->create([
        'name' => 'Test Component Group 1',
        'visible' => true,
    ]);

    ComponentGroup::factory()->create([
        'name' => 'Test Component Group 2',
        'visible' => false,
    ]);

    $component = livewire(Components::class);

    $component->assertSuccessful();

    assertCount(1, $component->componentGroups);

    $component->assertSee('Test Component Group 1');
    $component->assertDontSee('Test Component Group 2');
});

it('will only show enabled components', function () {
    $componentGroup = ComponentGroup::factory()->create([
        'name' => 'Laravel',
        'visible' => true,
    ]);

    Component::factory()->create([
        'name' => 'Forge',
        'component_group_id' => $componentGroup->id,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'name' => 'Cloud',
        'component_group_id' => $componentGroup->id,
        'enabled' => false,
    ]);

    $component = livewire(Components::class);

    $component->assertSuccessful();

    assertCount(1, $component->componentGroups->first()->components);

    $component->assertSee('Forge');
    $component->assertDontSee('Cloud');
});

it('can save status of component to have major outage', function () {
    $componentGroup = ComponentGroup::factory()->create([
        'name' => 'Laravel',
        'visible' => true,
    ]);

    $component = Component::factory()->create([
        'name' => 'Forge',
        'component_group_id' => $componentGroup->id,
        'status' => ComponentStatusEnum::operational,
    ]);

    $livewireComponent = livewire(Components::class);

    $livewireComponent->assertSuccessful();

    $livewireComponent->set(
        'formData.' . $componentGroup->id . '.components.' . $component->id . '.status',
        ComponentStatusEnum::major_outage->value
    );

    assertEquals(ComponentStatusEnum::major_outage, $component->fresh()->status);
});