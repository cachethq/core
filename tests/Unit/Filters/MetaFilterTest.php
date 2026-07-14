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

it('matches integer, float and boolean values from query-string input', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['priority' => 3, 'uptime' => 99.9, 'critical' => true]);

    expect(applyMetaFilter(['priority' => '3'])->pluck('id')->all())->toBe([$component->id])
        ->and(applyMetaFilter(['uptime' => '99.9'])->pluck('id')->all())->toBe([$component->id])
        ->and(applyMetaFilter(['critical' => 'true'])->pluck('id')->all())->toBe([$component->id])
        ->and(applyMetaFilter(['priority' => '4'])->count())->toBe(0)
        ->and(applyMetaFilter(['critical' => 'false'])->count())->toBe(0);
});

it('still matches string values that look numeric', function () {
    $component = Component::factory()->create();
    $component->syncMeta(['build' => '42']);

    expect(applyMetaFilter(['build' => '42'])->pluck('id')->all())->toBe([$component->id]);
});

function applyMetaFilter(array $value)
{
    $query = Component::query();

    (new MetaFilter)($query, $value, 'meta');

    return $query->get();
}
