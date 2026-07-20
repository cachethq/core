<?php

namespace Cachet\Mcp\Tools\Schedules;

use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Schedule;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GetSchedule extends Tool
{
    use PresentsResources;

    protected string $name = 'get_schedule';

    protected string $description = 'Get a single maintenance schedule by ID, including its updates and affected components.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The schedule ID.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $schedule = Schedule::query()->with(['components', 'updates'])->find($id = $request->integer('id'));

        if ($schedule === null) {
            return Response::error("Schedule [{$id}] not found.");
        }

        return Response::structured(['data' => $this->presentSchedule($schedule)]);
    }
}
