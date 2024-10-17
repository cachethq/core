<?php

namespace Cachet\Settings\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Description
{
    public function __construct(private readonly string $default, private readonly bool $required = true) {}

    public function getDefault(): string
    {
        return $this->default;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }
}