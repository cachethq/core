<?php

namespace Cachet\Mcp\Tools\Schedules;

use Cachet\Actions\Schedule\DeleteSchedule as DeleteScheduleAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class DeleteSchedule extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'delete_schedule';

    protected string $description = 'Delete a maintenance schedule. Requires the schedules.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The schedule ID.'),
        ];
    }

    public function handle(Request $request, DeleteScheduleAction $action): Response
    {
        if (! $this->tokenCan('schedules.delete')) {
            return $this->missingAbility('schedules.delete');
        }

        $schedule = Schedule::query()->find($id = $request->integer('id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$id}] not found.");
        }

        $action->handle($schedule);

        return Response::text("Schedule [{$id}] deleted.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedules.delete');
    }
}
