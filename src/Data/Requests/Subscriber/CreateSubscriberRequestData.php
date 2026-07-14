<?php

namespace Cachet\Data\Requests\Subscriber;

use Cachet\Data\BaseData;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateSubscriberRequestData extends BaseData
{
    public function __construct(
        public readonly string $email,
        public readonly ?bool $global = null,
        public readonly ?array $components = null,
        public readonly ?bool $verified = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $meta = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'global' => ['bool'],
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
            'verified' => ['bool'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
