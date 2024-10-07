<?php

declare(strict_types=1);

namespace Cachet\Data;

use Spatie\LaravelData\Data;

abstract class BaseData extends Data
{
    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
