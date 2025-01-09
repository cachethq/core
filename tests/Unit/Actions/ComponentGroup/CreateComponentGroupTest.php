<?php

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Enums\ResourceVisibilityEnum;
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
    $data = CreateComponentGroupRequestData::from([
        'name' => 'Services',
        'order' => 2,
        'visible' => ResourceVisibilityEnum::authenticated->value,
    ]);

    $componentGroup = app(CreateComponentGroup::class)->handle($data);

    expect($componentGroup)
        ->name->toBe($data->name)
        ->order->toBe($data->order)
        ->visible->toBe(ResourceVisibilityEnum::authenticated);
});

it('can create a component group and add components', function () {
    $components = Component::factory()->count(3)->create();

    $data = CreateComponentGroupRequestData::from([
        'name' => 'Services',
        'components' => $components->pluck('id')->values()->all(),
    ]);

    $componentGroup = app(CreateComponentGroup::class)->handle($data);

    $this->assertDatabaseHas('components', [
        'component_group_id' => $componentGroup->id,
    ]);
});
