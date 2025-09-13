<?php

namespace Tests\Unit;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\SystemStatusEnum;
use Cachet\Models\Component;
use Cachet\Status;

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
