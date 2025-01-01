<?php

namespace Cachet\Settings\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Description
{
    public function __construct(private readonly string $default, private readonly bool $required = true) {}

    public function default(): string
    {
        return $this->default;
    }

    public function required(): bool
    {
        return $this->required;
    }
}