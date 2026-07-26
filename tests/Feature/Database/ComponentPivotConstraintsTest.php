<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Return the pivots to the state they were in before the constraint migration.
 */
function dropPivotUniques(): void
{
    Schema::table('incident_components', function (Blueprint $table) {
        $table->dropUnique(['incident_id', 'component_id']);
    });

    Schema::table('schedule_components', function (Blueprint $table) {
        $table->dropUnique(['schedule_id', 'component_id']);
    });
}

/**
 * Run the constraint migration over whatever rows are present.
 */
function applyPivotConstraints(): void
{
    $migration = require __DIR__.'/../../../database/migrations/2026_07_25_000002_add_constraints_to_component_pivots.php';

    $migration->up();
}

it('collapses duplicate impacts to the most severe when the constraint is added', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->create();

    dropPivotUniques();

    foreach ([ComponentStatusEnum::performance_issues, ComponentStatusEnum::major_outage, ComponentStatusEnum::operational] as $status) {
        DB::table('incident_components')->insert([
            'incident_id' => $incident->id,
            'component_id' => $component->id,
            'component_status' => $status->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    applyPivotConstraints();

    $rows = DB::table('incident_components')
        ->where('incident_id', $incident->id)
        ->where('component_id', $component->id)
        ->get();

    expect($rows)->toHaveCount(1)
        ->and((int) $rows->first()->component_status)->toBe(ComponentStatusEnum::major_outage->value);
});

it('leaves rows that are already unique alone', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->create();

    $incident->components()->attach($component->id, [
        'component_status' => ComponentStatusEnum::partial_outage,
    ]);

    dropPivotUniques();
    applyPivotConstraints();

    expect(DB::table('incident_components')->count())->toBe(1)
        ->and((int) DB::table('incident_components')->first()->component_status)
        ->toBe(ComponentStatusEnum::partial_outage->value);
});

it('refuses to attach the same component to an incident twice', function () {
    $component = Component::factory()->create();
    $incident = Incident::factory()->create();

    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::major_outage]);
    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::partial_outage]);
})->throws(UniqueConstraintViolationException::class);
