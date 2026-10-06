<?php

namespace Cachet\Data\Requests\IncidentUpdate;

use Cachet\Data\BaseData;
use Cachet\Data\Requests\Incident\IncidentComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateIncidentUpdateRequestData extends BaseData
{
    public function __construct(
        public readonly IncidentStatusEnum $status,
        public readonly string $message,
        /** @var array<int, IncidentComponentRequestData> */
        #[DataCollectionOf(IncidentComponentRequestData::class)]
        public readonly array $components = [],
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'status' => ['required', Rule::enum(IncidentStatusEnum::class)],
            'message' => ['required', 'string'],
            /**
             * The components impacted by the incident, with the status to move them to.
             *
             * @var array<int, array{id: int, status: int}>
             *
             * @example [{"id": 1, "status": 3}]
             */
            'components' => ['array'],
            'components.*.id' => ['required', 'int', 'distinct', 'exists:components,id'],
            'components.*.status' => ['required', Rule::enum(ComponentStatusEnum::class)],
        ];
    }
}
