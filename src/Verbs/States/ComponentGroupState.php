<?php

namespace Cachet\Verbs\States;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Thunk\Verbs\State;

class ComponentGroupState extends State
{
    public string $name;

    public int $order = 0;

    public ComponentGroupVisibilityEnum $collapsed;

    public ResourceVisibilityEnum $visible;

    public bool $deleted = false;

    /** @var list<int> */
    public array $component_ids = [];
}
