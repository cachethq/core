<?php

use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;

it('tags components, incidents, metrics, and schedules', function (): void {
    $models = [
        Component::factory()->create(),
        Incident::factory()->create(),
        Metric::factory()->create(),
        Schedule::factory()->create(),
    ];

    foreach ($models as $model) {
        $model->syncTags(['API', 'Database', 'api']);

        expect($model->fresh()->tags->pluck('name')->all())->toBe(['API', 'Database']);
    }
});

it('filters resources by any supplied tag', function (): void {
    $api = Component::factory()->create();
    $api->syncTags(['API']);

    $database = Component::factory()->create();
    $database->syncTags(['Database']);

    expect(Component::query()->withAnyTags(['api', 'database'])->pluck('id')->all())
        ->toContain($api->id, $database->id);
});

it('reuses tags regardless of casing across separate syncs', function (): void {
    $first = Component::factory()->create();
    $second = Component::factory()->create();

    $first->syncTags(['API']);
    $second->syncTags(['api']);

    expect($second->fresh()->tags->sole()->id)->toBe($first->fresh()->tags->sole()->id);
});

it('filters components by tags through the API', function (): void {
    $api = Component::factory()->create(['name' => 'Public API']);
    $api->syncTags(['API']);

    $database = Component::factory()->create(['name' => 'Database']);
    $database->syncTags(['Database']);

    $this->getJson('/status/api/components?filter[tags]=api,database')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('regenerates a tag slug when its name changes', function (): void {
    $component = Component::factory()->create();
    $component->syncTags(['Public API']);

    $tag = $component->tags->first();
    $tag->update(['name' => 'Developer API']);

    expect($tag->refresh()->slug)->toBe('developer-api');
});
