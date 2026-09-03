<?php

use Cachet\Models\Component;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('indexes the package query hot paths', function (string $table, string $index) {
    $indexNames = collect(Schema::getIndexes($table))->pluck('name');

    expect($indexNames)->toContain($index);
})->with([
    ['components', 'components_monitoring_index'],
    ['component_checks', 'component_checks_component_checked_index'],
    ['incidents', 'incidents_status_created_index'],
    ['incidents', 'incidents_timeline_occurred_index'],
    ['incidents', 'incidents_timeline_created_index'],
    ['incidents', 'incidents_publish_index'],
    ['metric_points', 'metric_points_metric_created_index'],
    ['updates', 'updates_updateable_created_index'],
    ['schedules', 'schedules_completed_at_index'],
    ['schedules', 'schedules_scheduled_at_index'],
    ['schedules', 'schedules_publish_index'],
    ['schedule_components', 'schedule_components_component_id_index'],
    ['webhook_attempts', 'webhook_attempts_subscription_created_index'],
    ['webhook_attempts', 'webhook_attempts_created_at_index'],
    ['taggables', 'taggables_tag_unique'],
]);

it('enforces tag slug uniqueness', function () {
    $uniqueSlugIndex = collect(Schema::getIndexes('tags'))->first(
        fn (array $index): bool => $index['unique'] && $index['columns'] === ['slug'],
    );

    expect($uniqueSlugIndex)->not->toBeNull();
});

it('enforces tag assignment uniqueness', function () {
    $taggableIndex = collect(Schema::getIndexes('taggables'))->first(
        fn (array $index): bool => $index['unique']
            && $index['columns'] === ['tag_id', 'taggable_type', 'taggable_id'],
    );

    expect($taggableIndex)->not->toBeNull();
});

it('preserves assignments while deduplicating tags during upgrades', function () {
    /** @var Migration $migration */
    $migration = require dirname(__DIR__, 3).'/database/migrations/2026_09_03_000001_add_query_indexes_and_unique_tags.php';
    $migration->down();

    $component = Component::factory()->create();
    $timestamp = now();
    $canonicalTagId = DB::table('tags')->insertGetId([
        'name' => 'API',
        'slug' => 'api',
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ]);
    $duplicateTagId = DB::table('tags')->insertGetId([
        'name' => 'Public API',
        'slug' => 'api',
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ]);

    DB::table('taggables')->insert([
        [
            'tag_id' => $canonicalTagId,
            'taggable_type' => $component->getMorphClass(),
            'taggable_id' => $component->getKey(),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'tag_id' => $duplicateTagId,
            'taggable_type' => $component->getMorphClass(),
            'taggable_id' => $component->getKey(),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
    ]);

    $migration->up();

    expect(DB::table('tags')->where('slug', 'api')->count())->toBe(1)
        ->and(DB::table('taggables')->where('taggable_id', $component->getKey())->count())->toBe(1)
        ->and(DB::table('taggables')->where('tag_id', $canonicalTagId)->exists())->toBeTrue();
});
