<?php

namespace Cachet\QueryBuilders;

use Cachet\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * {@inheritDoc}
 *
 * @method static \Cachet\QueryBuilders\ScheduleBuilder incomplete()
 * @method static \Cachet\QueryBuilders\ScheduleBuilder inProgress()
 * @method static \Cachet\QueryBuilders\ScheduleBuilder inTheFuture()
 * @method static \Cachet\QueryBuilders\ScheduleBuilder inThePast()
 *
 * @mixin Schedule
 */
class ScheduleBuilder extends Builder
{
    /**
     * Scope schedules that are incomplete.
     */
    public function incomplete(): self
    {
        $this->where(function (ScheduleBuilder $query) {
            $query->where('completed_at', '>=', Carbon::now())
                ->orWhereNull('completed_at');
        });

        return $this;
    }

    /**
     * Scope schedules that are in progress.
     */
    public function inProgress(): self
    {
        $this->where('scheduled_at', '<=', Carbon::now())
            ->where(function (ScheduleBuilder $query) {
                $query->where('completed_at', '>=', Carbon::now())
                    ->orWhereNull('completed_at');
            });

        return $this;
    }

    /**
     * Scopes schedules to those in the future.
     */
    public function inTheFuture(): self
    {
        $this->where('scheduled_at', '>=', Carbon::now());

        return $this;
    }

    /**
     * Scopes schedules to those scheduled in the past.
     */
    public function inThePast(): self
    {
        $this->where('completed_at', '<=', Carbon::now())
            ->whereNotNull('completed_at');

        return $this;
    }
}
