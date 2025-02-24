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
     * @return void
     */
    public function __invoke(Builder $query, $value, string $property)
    {
        if (is_array($value)) {
            $this->multiValueFilter($value, $query);

            return;
        }
        $enum = $this->toEnum($value);
        if (! $enum instanceof ScheduleStatusEnum) {
            return;
        }
        $this->toQuery($enum, $query);
    }

    public function multiValueFilter(array $value, ScheduleBuilder $query)
    {
        $query->where(function (ScheduleBuilder $query) use ($value) {
            foreach ($value as $status) {
                $status = $this->toEnum($status);
                if (! $status instanceof ScheduleStatusEnum) {
                    continue;
                }

                $query->orWhere(function (ScheduleBuilder $query) use ($status) {
                    $this->toQuery($status, $query);
                });
            }
        });
    }

    protected function toQuery(ScheduleStatusEnum $value, ScheduleBuilder $query)
    {
        match ($value) {
            ScheduleStatusEnum::complete => $query->inThePast(),
            ScheduleStatusEnum::in_progress => $query->inProgress(),
            ScheduleStatusEnum::upcoming => $query->inTheFuture(),
        };
    }

    protected function toEnum($value)
    {
        if (! $value instanceof ScheduleStatusEnum && is_numeric($value)) {
            return ScheduleStatusEnum::tryFrom($value);
        }

        return $value;
    }
}
