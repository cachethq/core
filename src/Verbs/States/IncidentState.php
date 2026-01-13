<?php

namespace Cachet\Verbs\States;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Thunk\Verbs\State;

class IncidentState extends State
{
    public string $guid;

    public string $name;

    public IncidentStatusEnum $status;

    public string $message;

    public ResourceVisibilityEnum $visible;

    public bool $stickied = false;

    public bool $notifications = false;

    public ?string $occurred_at = null;

    public ?int $user_id = null;

    public ?string $external_provider = null;

    public ?string $external_id = null;

    public bool $deleted = false;

    /** @var array<int, ComponentStatusEnum> Component ID => status during incident */
    public array $affected_components = [];

    /** @var array<int, array{status: IncidentStatusEnum, message: string, user_id: ?int, at: string}> */
    public array $updates = [];

    /** @var array<int, array{from: ?IncidentStatusEnum, to: IncidentStatusEnum, at: string}> */
    public array $status_history = [];
}
