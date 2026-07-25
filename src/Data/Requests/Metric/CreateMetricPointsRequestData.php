<?php

namespace Cachet\Data\Requests\Metric;

use Cachet\Data\BaseData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateMetricPointsRequestData extends BaseData
{
    /**
     * @param  DataCollection<int, CreateMetricPointRequestData>  $points
     */
    public function __construct(
        #[DataCollectionOf(CreateMetricPointRequestData::class)]
        public readonly DataCollection $points,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            /**
             * The points to record. Points falling into the same bucket are
             * combined, so a batch costs one write per bucket it touches.
             */
            'points' => [
                'required',
                'array',
                'min:1',
                'max:'.(int) config('cachet.metrics.max_batch_points', 1000),
            ],
        ];
    }
}
