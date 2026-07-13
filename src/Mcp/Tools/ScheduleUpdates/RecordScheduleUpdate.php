<?php

namespace Cachet\Mcp\Tools\ScheduleUpdates;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class RecordScheduleUpdate extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'record_schedule_update';

    protected string $description = 'Post an update to an existing maintenance schedule. Providing completed_at marks the maintenance as complete. Requires the schedule-updates.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'schedule_id' => $schema->integer()->required()->description('The schedule ID.'),
            'message' => $schema->string()->required()->description('The update message, in Markdown.'),
            'completed_at' => $schema->string()->description('When the maintenance finished, formatted as Y-m-d H:i:s. Marks the schedule as complete.'),
        ];
    }

    public function handle(Request $request, CreateUpdate $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('schedule-updates.manage')) {
            return $this->missingAbility('schedule-updates.manage');
        }

        $schedule = Schedule::query()->find($id = $request->integer('schedule_id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$id}] not found.");
        }

        $data = CreateScheduleUpdateRequestData::validateAndCreate($request->only(['message', 'completed_at']));

        return Response::structured(['data' => $this->presentUpdate($action->handle($schedule, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedule-updates.manage');
    }
}
