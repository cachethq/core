<?php

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Enums\ComponentGroupVisibilityEnum;

it('can create a component group with just a name', function () {
    $data = [
        'name' => 'Services',
    ];

    $componentGroup = CreateComponentGroup::run($data);

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

    $componentGroup = CreateComponentGroup::run($data);

    expect($componentGroup)
        ->name->toBe($data['name'])
        ->order->toBe($data['order'])
        ->visible->toBe(ComponentGroupVisibilityEnum::expanded);
});
