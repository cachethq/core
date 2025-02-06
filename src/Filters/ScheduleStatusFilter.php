<?php

namespace Cachet\Filters;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\QueryBuilders\ScheduleBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ScheduleStatusFilter implements Filter
{
    /**
     * @param  ScheduleBuilder  $query
     * @param $value
     * @param  string  $property
     * @return void
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        match($value) {
            (string) ScheduleStatusEnum::complete->value,
            ScheduleStatusEnum::complete->name => $query->inThePast(),
            (string) ScheduleStatusEnum::in_progress->value,
            ScheduleStatusEnum::in_progress->name => $query->inProgress(),
            (string) ScheduleStatusEnum::upcoming->value,
            ScheduleStatusEnum::upcoming->name => $query->inTheFuture(),
            default => null,
        };
    }

}
