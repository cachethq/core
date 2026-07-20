<?php

namespace Cachet\Mcp\Tools\Schedules;

use Cachet\Actions\Schedule\CreateSchedule as CreateScheduleAction;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateSchedule extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_schedule';

    protected string $description = 'Create a new maintenance schedule, optionally linking affected components with the status to show during the window. Requires the schedules.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->max(255)->required()->description('The name of the maintenance schedule.'),
            'message' => $schema->string()->required()->description('The schedule message, in Markdown.'),
            'scheduled_at' => $schema->string()->required()->description('When the maintenance starts, as an ISO-8601 or Y-m-d H:i:s datetime.'),
            'completed_at' => $schema->string()->description('When the maintenance finished, as an ISO-8601 or Y-m-d H:i:s datetime. Leave empty for incomplete maintenance.'),
            'notifications' => $schema->boolean()->default(false)->description('Whether to notify verified subscribers.'),
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

    public function handle(Request $request, CreateScheduleAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('schedules.manage')) {
            return $this->missingAbility('schedules.manage');
        }

        $data = CreateScheduleRequestData::validateAndCreate($request->all());

        $schedule = $action->handle($data)->load(['components', 'updates']);

        return Response::structured(['data' => $this->presentSchedule($schedule)]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('schedules.manage');
    }
}
