<?php

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

it('has a group', function () {
    $component = Component::factory()->forGroup([
        'name' => 'my component group',
    ])->create();

    expect($component->group)
        ->toBeInstanceOf(ComponentGroup::class)
        ->name->toBe('my component group');
});

it('has incidents', function () {
    $component = Component::factory()->hasIncidents(2)->create();

    expect($component->incidents)->toHaveCount(2);
});

it('casts meta to json', function () {
    $component = Component::factory()->withMeta()->create();

    expect($component)
        ->meta->toBe([
            'foo' => 'bar',
        ]);
});
