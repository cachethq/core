<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Return the status column to its pre-backfill nullable shape.
 */
function makeComponentStatusNullable(): void
{
    Schema::table('components', function (Blueprint $table) {
        $table->unsignedInteger('status')->nullable()->change();
    });
}

/**
 * Run the component status backfill over whatever rows are present.
 */
function backfillComponentStatuses(): void
{
    $migration = require __DIR__.'/../../../database/migrations/2026_07_26_000001_backfill_component_status_and_make_it_required.php';

    $migration->up();
}

it('backfills null component statuses to unknown before making the column required', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    makeComponentStatusNullable();

    DB::table('components')->where('id', $component->id)->update(['status' => null]);

    backfillComponentStatuses();

    expect($component->fresh()->status)->toBe(ComponentStatusEnum::unknown);
});
