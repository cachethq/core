<?php

namespace Cachet\Data\IncidentTemplate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\Validation\Max;

final class UpdateIncidentTemplateData extends BaseData
{
    public function __construct(
        #[Max(255)]
        public readonly ?string $name = null,
        public readonly ?string $template = null,
        private readonly ?string $slug = null,
        public readonly ?IncidentTemplateEngineEnum $engine = null,
    ) {}

    public function slug(): string
    {
        return $this->slug ?? Str::slug($this->name);
    }
}
