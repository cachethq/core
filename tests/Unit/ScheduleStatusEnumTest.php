<?php

use Cachet\Enums\ScheduleStatusEnum;

it('returns only the upcoming status from upcoming', function () {
    expect(ScheduleStatusEnum::upcoming())->toBe([
        ScheduleStatusEnum::upcoming->value,
    ]);
});
