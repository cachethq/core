<?php

namespace Cachet\Data\Requests\Component;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateComponentRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?ComponentStatusEnum $status = null,
        public readonly ?string $link = null,
        public readonly ?int $order = null,
        public readonly bool $enabled = true,
        public readonly ?int $componentGroupId = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'description' => ['string'],
            'status' => [Rule::enum(ComponentStatusEnum::class)],
            'link' => ['string'],
            'order' => ['int', 'min:0'],
            'component_group_id' => ['int', 'min:0', Rule::exists('component_groups', 'id')],
            'enabled' => ['boolean'],
        ];
    }
}
