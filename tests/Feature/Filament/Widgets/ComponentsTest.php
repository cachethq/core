<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Filament\Widgets\Components;
use Cachet\Models\ComponentGroup;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

it('component smoke test', function () {
    $component = livewire(Components::class);

    $component->assertSuccessful();
})->only();

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
    $component->assertDontSee('Test Component Group 1');
});