<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\SystemStatusEnum;

it('maps component statuses to badge colors', function (ComponentStatusEnum $status, string $color) {
    expect($status->getBadgeColor())->toBe($color);
})->with([
    [ComponentStatusEnum::operational, 'brightgreen'],
    [ComponentStatusEnum::performance_issues, 'yellow'],
    [ComponentStatusEnum::partial_outage, 'orange'],
    [ComponentStatusEnum::major_outage, 'red'],
    [ComponentStatusEnum::unknown, 'lightgray'],
    [ComponentStatusEnum::under_maintenance, 'orange'],
]);

it('maps system statuses to badge colors', function (SystemStatusEnum $status, string $color) {
    expect($status->getBadgeColor())->toBe($color);
})->with([
    [SystemStatusEnum::operational, 'brightgreen'],
    [SystemStatusEnum::partial_outage, 'orange'],
    [SystemStatusEnum::major_outage, 'red'],
    [SystemStatusEnum::under_maintenance, 'orange'],
]);
