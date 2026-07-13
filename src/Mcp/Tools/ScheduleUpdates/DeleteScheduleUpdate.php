<?php

namespace Cachet\Mcp\Tools\ScheduleUpdates;

use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteScheduleUpdate extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_schedule_update';

    protected string $description = 'Delete an update from a maintenance schedule. Requires the schedule-updates.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'schedule_id' => $schema->integer()->required()->description('The schedule ID.'),
            'update_id' => $schema->integer()->required()->description('The schedule update ID.'),
        ];
    }

    public function handle(Request $request, DeleteUpdate $action): Response
    {
        if (! $this->tokenCan('schedule-updates.delete')) {
            return $this->missingAbility('schedule-updates.delete');
        }

        $schedule = Schedule::query()->find($scheduleId = $request->integer('schedule_id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$scheduleId}] not found.");
        }

        $update = $schedule->updates()->find($updateId = $request->integer('update_id'));

        if ($update === null) {
            return Response::error("Update [{$updateId}] not found on schedule [{$scheduleId}].");
        }

        $action->handle($update);

        return Response::text("Update [{$updateId}] deleted from schedule [{$scheduleId}].");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedule-updates.delete');
    }
}
