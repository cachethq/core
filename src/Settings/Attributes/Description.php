<?php

namespace Cachet\Settings\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Description
{
    public function __construct(private string $description) {}
}