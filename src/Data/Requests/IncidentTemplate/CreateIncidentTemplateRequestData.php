<?php

namespace Cachet\Data\Requests\IncidentTemplate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class CreateIncidentTemplateRequestData extends BaseData
{
    private readonly ?string $slug;

    public function __construct(
        public readonly string $name,
        public readonly string $template,
        ?string $slug = null,
        public readonly ?IncidentTemplateEngineEnum $engine = null,
    ) {
        $this->slug = $slug;
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['string'],
            'template' => ['required', 'string'],
            'engine' => [Rule::enum(IncidentTemplateEngineEnum::class)],
        ];
    }

    public function slug(): string
    {
        return $this->slug ?? Str::slug($this->name);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'slug' => $this->slug(),
        ]);
    }
}
