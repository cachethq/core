<?php

use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

it('can update a component group with just a name', function () {
    $componentGroup = ComponentGroup::factory()->create();

    $data = [
        'name' => 'Services',
    ];

    $componentGroup = app(UpdateComponentGroup::class)->handle($componentGroup, $data);

    expect($componentGroup)
        ->name->toBe($data['name']);
});

it('can update a component group without touching components', function () {
    Component::factory()->count(3)->create();
    $componentGroup = ComponentGroup::factory()->create();

    $data = [
        'name' => 'Services',
    ];

    $componentGroup = app(UpdateComponentGroup::class)->handle($componentGroup, $data);

    expect($componentGroup)
        ->name->toBe($data['name']);
    $this->assertDatabaseMissing('components', [
        'component_group_id' => $componentGroup->id,
    ]);
});

it('can update a component group with components', function () {
    $components = Component::factory()->count(3)->create();
    $componentGroup = ComponentGroup::factory()->create();

    $componentGroup = app(UpdateComponentGroup::class)->handle($componentGroup, [], $components->pluck('id')->values()->all());

    $this->assertDatabaseHas('components', [
        'component_group_id' => $componentGroup->id,
    ]);
});
