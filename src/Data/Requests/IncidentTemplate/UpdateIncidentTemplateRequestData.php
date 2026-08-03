<?php

namespace Cachet\Data\Requests\IncidentTemplate;

use Cachet\Data\BaseData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class UpdateIncidentTemplateRequestData extends BaseData
{
    private readonly ?string $slug;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $template = null,
        ?string $slug = null,
        public readonly ?IncidentTemplateEngineEnum $engine = null,
    ) {
        $this->slug = $slug;
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['string', 'max:255'],
            'slug' => ['string'],
            'template' => ['string'],
            'engine' => [Rule::enum(IncidentTemplateEngineEnum::class)->only(IncidentTemplateEngineEnum::available())],
        ];
    }

    /**
     * The slug to store, or null when the payload gives no basis for one.
     *
     * An explicit slug always wins; otherwise a new name regenerates the
     * slug. A payload with neither must leave the stored slug untouched
     * rather than derive one from nothing.
     */
    public function slug(): ?string
    {
        if ($this->slug !== null) {
            return $this->slug;
        }

        return $this->name === null ? null : Str::slug($this->name);
    }

    public function toArray(): array
    {
        $slug = $this->slug();

        return array_merge(parent::toArray(), $slug === null ? [] : ['slug' => $slug]);
    }
}
