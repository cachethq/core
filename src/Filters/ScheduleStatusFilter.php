<?php

namespace Cachet\Filters;

use Cachet\Enums\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ScheduleStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        match($value) {
            ScheduleStatusEnum::complete,
            'complete' => $query->inThePast(),
            ScheduleStatusEnum::in_progress,
            'in_progress' => $query->inProgress(),
            ScheduleStatusEnum::upcoming,
            'upcoming' => $query->inTheFuture(),
        };
    }

}
