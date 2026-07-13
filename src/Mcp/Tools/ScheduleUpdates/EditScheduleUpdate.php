<?php

namespace Cachet\Mcp\Tools\ScheduleUpdates;

use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class EditScheduleUpdate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'edit_schedule_update';

    protected string $description = 'Edit an existing update on a maintenance schedule. Requires the schedule-updates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'schedule_id' => $schema->integer()->required()->description('The schedule ID.'),
            'update_id' => $schema->integer()->required()->description('The schedule update ID.'),
            'message' => $schema->string()->description('The update message, in Markdown.'),
        ];
    }

    public function handle(Request $request, EditUpdate $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('schedule-updates.manage')) {
            return $this->missingAbility('schedule-updates.manage');
        }

        $schedule = Schedule::query()->find($scheduleId = $request->integer('schedule_id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$scheduleId}] not found.");
        }

        $update = $schedule->updates()->find($updateId = $request->integer('update_id'));

        if ($update === null) {
            return Response::error("Update [{$updateId}] not found on schedule [{$scheduleId}].");
        }

        $data = EditScheduleUpdateRequestData::validateAndCreate($request->only(['message']));

        return Response::structured(['data' => $this->presentUpdate($action->handle($update, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedule-updates.manage');
    }
}
