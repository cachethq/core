<?php

namespace Cachet\Data\Metrics;

use Cachet\Data\BaseData;
use Cachet\Data\Casts\FlexibleDateTimeCast;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;

/**
 * A single observation of a metric, before it is folded into a point.
 *
 * This is the unit of ingestion: the recorder decides which bucket the
 * observation lands in and how it combines with the observations already
 * in that bucket. The "source" is free-form and only describes where the
 * observation came from, for example the monitoring package that sent it.
 */
final class MetricObservation extends BaseData
{
    public function __construct(
        public readonly float $value,
        #[WithCast(FlexibleDateTimeCast::class)]
        public readonly ?CarbonInterface $recordedAt = null,
        public readonly ?string $source = null,
    ) {}

    /**
     * When the observation was recorded, defaulting to now.
     */
    public function timestamp(): CarbonInterface
    {
        return $this->recordedAt ?? Carbon::now();
    }
}
