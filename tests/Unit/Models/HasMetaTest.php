<?php

use Cachet\Models\Component;

it('synchronizes metadata while preserving value types', function () {
    $component = Component::factory()->create();

    $component->syncMeta([
        'region' => 'eu-west',
        'priority' => 3,
        'enabled' => true,
        'options' => ['replicas' => 2],
    ]);

    expect($component->metaValues())->toBe([
        'region' => 'eu-west',
        'priority' => 3,
        'enabled' => true,
        'options' => ['replicas' => 2],
    ]);
});

it('updates supplied metadata and removes omitted keys', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['region' => 'eu-west', 'stale' => true]);

    $component->load('meta');
    $component->syncMeta(['region' => 'us-east']);

    expect($component->relationLoaded('meta'))->toBeFalse()
        ->and($component->metaValues())->toBe(['region' => 'us-east']);
});

it('retains metadata when its owner is soft deleted', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['region' => 'eu-west']);
    $metaId = $component->meta()->sole()->id;

    $component->delete();

    $this->assertDatabaseHas('meta', ['id' => $metaId]);
});

it('deletes metadata when its owner is force deleted', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['region' => 'eu-west']);
    $metaId = $component->meta()->sole()->id;

    $component->forceDelete();

    $this->assertDatabaseMissing('meta', ['id' => $metaId]);
});
