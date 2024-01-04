<?php

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Models\Component;

it('can create a component group with just a name', function () {
    $data = [
        'name' => 'Services',
    ];

    $componentGroup = app(CreateComponentGroup::class)->handle($data);

    expect($componentGroup)
        ->name->toBe($data['name'])
        ->order->toBeNull()
        ->visible->toBeNull();
})->todo('Make visible default to non-null value?');

it('can create a component group with a name, order and visibility', function () {
    $data = [
        'name' => 'Services',
        'order' => 2,
        'visible' => ComponentGroupVisibilityEnum::expanded,
    ];

    $componentGroup = app(CreateComponentGroup::class)->handle($data);

    expect($componentGroup)
        ->name->toBe($data['name'])
        ->order->toBe($data['order'])
        ->visible->toBe(ComponentGroupVisibilityEnum::expanded);
});

it('can create a component group and add components', function () {
    $data = [
        'name' => 'Services',
    ];

    $components = Component::factory()->count(3)->create();

    $componentGroup = app(CreateComponentGroup::class)->handle($data, $components->pluck('id')->values()->all());

    $this->assertDatabaseHas('components', [
        'component_group_id' => $componentGroup->id,
    ]);
});
