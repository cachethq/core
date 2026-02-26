<?php

namespace Cachet\Verbs\States;

use Cachet\Enums\ComponentStatusEnum;
use Thunk\Verbs\State;

class ComponentState extends State
{
    public string $name;

    public ?string $description = null;

    public ?string $link = null;

    public ComponentStatusEnum $status;

    public int $order = 0;

    public ?int $component_group_id = null;

    public bool $enabled = true;

    public array $meta = [];

    public bool $deleted = false;

    /** @var array<int, array{status: ComponentStatusEnum, at: string}> */
    public array $status_history = [];

    /** @var list<int> */
    public array $incident_ids = [];

    /** @var list<int> */
    public array $schedule_ids = [];
}
