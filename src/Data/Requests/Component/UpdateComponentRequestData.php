<?php

namespace Cachet\Data\Requests\Component;

use Cachet\Data\BaseUpdateData;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateComponentRequestData extends BaseUpdateData
{
    public function __construct(
        public readonly string|Optional $name = new Optional,
        public readonly string|Optional|null $description = new Optional,
        public readonly ComponentStatusEnum|Optional $status = new Optional,
        public readonly string|Optional|null $link = new Optional,
        public readonly int|Optional|null $order = new Optional,
        public readonly bool|Optional $enabled = new Optional,
        public readonly int|Optional|null $componentGroupId = new Optional,
        /** @var array<string, mixed>|Optional|null */
        public readonly array|Optional|null $meta = new Optional,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => [Rule::enum(ComponentStatusEnum::class)],
            'link' => ['nullable', 'string', 'url:http,https'],
            'order' => ['nullable', 'int', 'min:0'],
            'component_group_id' => ['nullable', 'int', 'min:0', Rule::exists('component_groups', 'id')],
            'enabled' => ['boolean'],
            /**
             * Key/value metadata to store against the resource.
             *
             * @var array<string, mixed>|null
             *
             * @example {"cluster": "eu-west"}
             */
            'meta' => ['nullable', 'array'],
        ];
    }
}
