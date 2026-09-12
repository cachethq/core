<?php

namespace Tests\Unit;

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Cachet\Status;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('will determine there is a major outage when number of components exceeds default outage threshold of 25%', function () {
    $status = new Status;

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    assertTrue($status->majorOutage());
});

it('will determine there is not a major outage when number of components does not exceed default outage threshold of 25%', function () {
    $status = new Status;

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
    ]);

    Component::factory()->times(4)->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    assertFalse($status->majorOutage());
});

it('can get the current system status', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::operational);
});

it('can get the current system status as under maintenance', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::under_maintenance->value,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::under_maintenance);
});

it('can get the current system status as partial outage', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::partial_outage->value,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::partial_outage);
});

it('can get the current system status as major outage', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::major_outage);
});

it('can fetch component overview', function () {
    Component::factory()
        ->sequence(
            ['status' => ComponentStatusEnum::unknown->value],
            ['status' => ComponentStatusEnum::operational->value],
            ['status' => ComponentStatusEnum::partial_outage->value],
            ['status' => ComponentStatusEnum::major_outage->value],
            ['status' => ComponentStatusEnum::under_maintenance->value],
        )
        ->count(5)
        ->create();

    $components = (new Status)->components();

    expect($components)
        ->total->toBe(5)
        ->operational->toBe(1)
        ->performance_issues->toBe(0)
        ->partial_outage->toBe(1)
        ->major_outage->toBe(1)
        ->under_maintenance->toBe(1);
});

it('returns the most recent enabled component update timestamp', function () {
    Component::factory()->create([
        'enabled' => false,
        'updated_at' => now(),
    ]);
    $component = Component::factory()->create([
        'enabled' => true,
        'updated_at' => now()->subMinute(),
    ]);

    $lastUpdated = (new Status)->lastUpdated();

    expect($lastUpdated)
        ->toBeInstanceOf(CarbonInterface::class)
        ->toEqual($component->updated_at);
});

it('considers incidents, incident updates and schedules when calculating the last updated timestamp', function () {
    expect((new Status)->lastUpdated())->toBeNull();

    Component::factory()->create([
        'enabled' => true,
        'updated_at' => now()->subWeek(),
    ]);
    $incident = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'updated_at' => now()->subDay(),
    ]);

    expect((new Status)->lastUpdated())->toEqual($incident->updated_at);

    $update = Update::factory()->forIncident($incident)->create([
        'status' => null,
        'created_at' => now()->subHour(),
        'updated_at' => now()->subHour(),
    ]);

    expect((new Status)->lastUpdated())->toEqual($update->updated_at);

    $schedule = Schedule::factory()->create([
        'updated_at' => now()->subMinute(),
    ]);

    expect((new Status)->lastUpdated())->toEqual($schedule->updated_at);
});

it('ignores activity hidden from guests when calculating the last updated timestamp', function () {
    $component = Component::factory()->create([
        'enabled' => true,
        'updated_at' => now()->subWeek(),
    ]);
    $hidden = Incident::factory()->create([
        'visible' => ResourceVisibilityEnum::authenticated,
        'updated_at' => now()->subMinute(),
    ]);
    Update::factory()->forIncident($hidden)->create([
        'status' => null,
        'created_at' => now()->subMinute(),
        'updated_at' => now()->subMinute(),
    ]);
    Incident::factory()->scheduled()->create([
        'visible' => ResourceVisibilityEnum::guest,
        'updated_at' => now()->subMinute(),
    ]);
    Schedule::factory()->scheduled()->create([
        'updated_at' => now()->subMinute(),
    ]);

    expect((new Status)->lastUpdated())->toEqual($component->updated_at);
});

it('caches the status aggregates and flushes them when status data changes', function () {
    Component::factory()->create([
        'enabled' => true,
    ]);

    $status = new Status;
    $status->components();
    $status->incidents();
    $status->lastUpdated();

    expect(Cache::has('cachet::status:components'))->toBeTrue()
        ->and(Cache::has('cachet::status:incidents'))->toBeTrue()
        ->and(Cache::has('cachet::status:last-updated'))->toBeTrue();

    Component::factory()->create([
        'enabled' => true,
    ]);

    expect(Cache::has('cachet::status:components'))->toBeFalse()
        ->and(Cache::has('cachet::status:incidents'))->toBeFalse()
        ->and(Cache::has('cachet::status:last-updated'))->toBeFalse()
        ->and((int) (new Status)->components()->total)->toBe(2);
});

it('excludes disabled components from component overview', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
        'enabled' => false,
    ]);

    $components = (new Status)->components();

    expect($components)
        ->total->toBe(1)
        ->operational->toBe(1)
        ->major_outage->toBe(0);
});

it('excludes disabled components from system status calculation', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::partial_outage->value,
        'enabled' => false,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::operational);
});

it('excludes disabled components from major outage calculation', function () {
    $status = new Status;

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
        'enabled' => false,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
        'enabled' => true,
    ]);

    assertFalse($status->majorOutage());
});

it('excludes disabled components from under maintenance status', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::under_maintenance->value,
        'enabled' => false,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
        'enabled' => true,
    ]);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::operational);
});

it('counts an incident with multiple updates once and resolves it by its latest update', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating->value,
        'visible' => ResourceVisibilityEnum::guest,
    ]);
    Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::identified->value,
    ]);
    Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::fixed->value,
    ]);

    app(SyncIncidentStatus::class)->handle($incident);

    $incidents = (new Status)->incidents();

    expect($incidents)
        ->total->toBe(1)
        ->resolved->toBe(1)
        ->unresolved->toBe(0);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::operational);
});

it('treats an incident as unresolved when its latest update is not fixed', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating->value,
    ]);
    Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::identified->value,
    ]);

    $incidents = (new Status)->incidents();

    expect($incidents)
        ->total->toBe(1)
        ->resolved->toBe(0)
        ->unresolved->toBe(1);

    $this->assertEquals((new Status)->current(), SystemStatusEnum::partial_outage);
});

it('ignores schedule updates when calculating the incident overview', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::fixed->value,
    ]);
    Update::factory()->forSchedule(Schedule::factory()->create())->create();

    $incidents = (new Status)->incidents();

    expect($incidents)
        ->total->toBe(1)
        ->resolved->toBe(1)
        ->unresolved->toBe(0);
});
