<?php

namespace Cachet\Verbs\States;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Thunk\Verbs\State;

class ScheduleState extends State
{
    public string $name;

    public ?string $message = null;

    public ?string $scheduled_at = null;

    public ?string $completed_at = null;

    public bool $deleted = false;

    /** @var array<int, ComponentStatusEnum> Component ID => status during maintenance */
    public array $affected_components = [];

    /** @var array<int, array{status: ?IncidentStatusEnum, message: string, user_id: ?int, at: string}> */
    public array $updates = [];
}
