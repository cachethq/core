<?php

use Cachet\Database\Seeders\DatabaseSeeder;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

it('assigns affected components to the demo maintenance schedules', function () {
    $this->seed(DatabaseSeeder::class);

    $documentationMaintenance = Schedule::query()
        ->where('name', 'Documentation Maintenance')
        ->firstOrFail();
    $databaseUpgrade = Schedule::query()
        ->where('name', 'Database Server Upgrade')
        ->firstOrFail();

    expect($documentationMaintenance->components)
        ->toHaveCount(1)
        ->first()->name->toBe('Cachet Documentation')
        ->and($documentationMaintenance->components->first()->pivot->component_status)
        ->toBe(ComponentStatusEnum::performance_issues)
        ->and($databaseUpgrade->components)
        ->toHaveCount(1)
        ->first()->name->toBe('Cachet Website')
        ->and($databaseUpgrade->components->first()->pivot->component_status)
        ->toBe(ComponentStatusEnum::partial_outage);
});

it('seeds polished incident update copy', function () {
    $this->seed(DatabaseSeeder::class);

    $message = Update::query()
        ->where('message', 'like', '%latest [blog post]%')
        ->value('message');

    expect($message)
        ->toContain('For more information, read our latest [blog post]')
        ->not->toContain('please you can read');
});

it('seeds a public documentation incident', function () {
    $this->seed(DatabaseSeeder::class);

    $incident = Incident::query()
        ->where('name', 'Documentation Search Unavailable')
        ->firstOrFail();

    expect($incident)
        ->status->toBe(IncidentStatusEnum::investigating)
        ->visible->toBe(ResourceVisibilityEnum::guest)
        ->and($incident->components)
        ->toHaveCount(1)
        ->first()->name->toBe('Cachet Documentation')
        ->and($incident->components->first()->pivot->component_status)
        ->toBe(ComponentStatusEnum::partial_outage);
});
