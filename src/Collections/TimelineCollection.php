<?php

namespace Cachet\Collections;

use Cachet\Contracts\Support\Sequencable;
use Illuminate\Support\Collection;

class TimelineCollection extends Collection
{


    public function getSorted($startDate, $endDate, $onlyDisruptedDays = false)
    {
        return $this->sortByDesc(fn (Sequencable $item) => $item->getSequenceTimestamp())
            ->groupBy(fn (Sequencable $item) => $item->getSequenceTimestamp()->toDateString())
            ->union(
            // Back-fill any missing dates...
                collect($endDate->toPeriod($startDate))
                    ->keyBy(fn ($period) => $period->toDateString())
                    ->map(fn ($period) => collect())
            )
            ->when($onlyDisruptedDays, fn ($collection) => $collection->filter(fn ($items) => $items->isNotEmpty()))
            ->sortKeysDesc();
    }
}
