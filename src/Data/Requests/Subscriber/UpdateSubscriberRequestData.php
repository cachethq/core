<?php

namespace Cachet\Data\Requests\Subscriber;

use Cachet\Data\BaseData;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateSubscriberRequestData extends BaseData
{
    public function __construct(
        public readonly ?string $email = null,
        public readonly ?bool $global = null,
        public readonly ?array $components = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => ['email', 'max:255'],
            'global' => ['bool'],
            'components' => ['array'],
            'components.*' => ['int', 'min:0', Rule::exists('components', 'id')],
        ];
    }
}
