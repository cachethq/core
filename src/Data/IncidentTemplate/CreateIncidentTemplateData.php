<?php

namespace Cachet\Data\IncidentTemplate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;

final class CreateIncidentTemplateData extends BaseData
{
    public function __construct(
        #[Max(255), Required]
        public readonly string $name,
        #[Required]
        public readonly string $template,
        private readonly ?string $slug = null,
        #[Enum(IncidentTemplateEngineEnum::class)]
        public readonly ?IncidentTemplateEngineEnum $engine = null,
    ) {}

    public function slug(): string
    {
        return $this->slug ?? Str::slug($this->name);
    }

    public function toArray(): array
    {
        return array_merge([
            'slug' => Str::slug($this->name),
        ], parent::toArray());
    }
}
