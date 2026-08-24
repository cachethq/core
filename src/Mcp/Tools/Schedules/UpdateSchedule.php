<?php

namespace Cachet\Mcp\Tools\Schedules;

use Cachet\Actions\Schedule\UpdateSchedule as UpdateScheduleAction;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Enums\ComponentStatusEnum;
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
class UpdateSchedule extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_schedule';

    protected string $description = 'Update an existing maintenance schedule. Set completed_at to mark the maintenance as complete. Requires the schedules.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The schedule ID.'),
            'tags' => $schema->array()->items($schema->string())->description('Replace the maintenance schedule tags.'),
            'name' => $schema->string()->max(255)->description('The name of the maintenance schedule.'),
            'message' => $schema->string()->description('The schedule message, in Markdown.'),
            'scheduled_at' => $schema->string()->description('When the maintenance starts, as an ISO-8601 or Y-m-d H:i:s datetime.'),
            'completed_at' => $schema->string()->description('When the maintenance finished, as an ISO-8601 or Y-m-d H:i:s datetime.'),
            'published_at' => $schema->string()->description('When to publish the maintenance, as an ISO-8601 or Y-m-d H:i:s datetime. While set in the future the maintenance is hidden from the status page and public API.'),
            'components' => $schema->array()
                ->items($schema->object([
                    'id' => $schema->integer()->required()->description('The component ID.'),
                    'status' => $schema->integer()
                        ->enum(array_column(ComponentStatusEnum::cases(), 'value'))
                        ->required()
                        ->description('The status to show for the component during the window: 1 operational, 2 performance issues, 3 partial outage, 4 major outage, 5 unknown, 6 under maintenance.'),
                ]))
                ->description('Affected components and the status to show for each.'),
        ];
    }

    public function handle(Request $request, UpdateScheduleAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('schedules.manage')) {
            return $this->missingAbility('schedules.manage');
        }

        $schedule = Schedule::query()->find($id = $request->integer('id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$id}] not found.");
        }

        $data = UpdateScheduleRequestData::validateAndCreate(
            $request->only(['name', 'message', 'scheduled_at', 'completed_at', 'published_at', 'components'])
        );

        return Response::structured(['data' => $this->presentSchedule($action->handle($schedule, $data))]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedules.manage');
    }
}
