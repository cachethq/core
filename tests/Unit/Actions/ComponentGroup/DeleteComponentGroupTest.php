<?php

use Cachet\Actions\ComponentGroup\DeleteComponentGroup;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

it('can delete a component group', function () {
    $componentGroup = ComponentGroup::factory()->create();

    app(DeleteComponentGroup::class)->handle($componentGroup);

    $this->assertDatabaseMissing('component_groups', [
        'id' => $componentGroup->id,
    ]);
});

it('resets the component_group_id for components in a deleted component group', function () {
    $component = Component::factory()->forGroup()->create();
    $componentGroupId = $component->component_group_id;

    app(DeleteComponentGroup::class)->handle($component->group);

    $this->assertDatabaseMissing('component_groups', [
        'id' => $componentGroupId,
    ]);
    $this->assertDatabaseHas('components', [
        'id' => $component->id,
        'component_group_id' => null,
    ]);
});

it('keeps components attached when the delete fails part-way', function () {
    $componentGroup = ComponentGroup::factory()->create();
    $component = Component::factory()->create(['component_group_id' => $componentGroup->id]);

    ComponentGroup::deleted(fn () => throw new RuntimeException('boom'));

    try {
        app(DeleteComponentGroup::class)->handle($componentGroup);
    } catch (RuntimeException) {
        //
    }

    $this->assertDatabaseHas('component_groups', ['id' => $componentGroup->id]);
    expect($component->fresh()->component_group_id)->toBe($componentGroup->id);
});
