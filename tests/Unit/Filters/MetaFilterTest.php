<?php

use Cachet\Filters\MetaFilter;
use Cachet\Models\Component;

it('filters records matching every provided key', function () {
    $match = Component::factory()->create();
    $match->syncMeta(['region' => 'eu-west', 'tier' => 'gold']);

    $other = Component::factory()->create();
    $other->syncMeta(['region' => 'eu-west', 'tier' => 'silver']);

    Component::factory()->create();

    expect(applyMetaFilter(['region' => 'eu-west', 'tier' => 'gold'])->pluck('id')->all())
        ->toBe([$match->id]);
});

it('ignores empty and non-scalar values', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['region' => 'eu-west']);

    expect(applyMetaFilter([])->count())->toBe(1)
        ->and(applyMetaFilter(['region' => ['nested']])->count())->toBe(1);
});

function applyMetaFilter(array $value)
{
    $query = Component::query();

    (new MetaFilter)($query, $value, 'meta');

    return $query->get();
}
