<?php

namespace Tests\Unit;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Status;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

it('will determine there is a major outage when number of components exceeds default outage threshold of 25%', function () {
    $status = new Status();

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
    ]);

    assertTrue($status->majorOutage());
})->only();

it('will determine there is not a major outage when number of components does not exceed default outage threshold of 25%', function () {
    $status = new Status();

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage->value,
    ]);

    Component::factory()->times(4)->create([
        'status' => ComponentStatusEnum::operational->value,
    ]);

    assertFalse($status->majorOutage());
})->only();