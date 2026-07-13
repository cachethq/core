<?php

namespace Cachet\Mcp\Tools\Subscribers;

use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\InteractsWithPagination;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Subscriber;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListSubscribers extends Tool
{
    use GuardsMcpAbilities;
    use InteractsWithPagination;
    use PresentsResources;

    protected string $name = 'list_subscribers';

    protected string $description = 'List status page subscribers, optionally filtered by email or global subscription state. Requires the subscribers.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'email' => $schema->string()->description('Filter by partial email address.'),
            'global' => $schema->boolean()->description('Filter by whether the subscriber is subscribed to all components.'),
            'per_page' => $schema->integer()->min(1)->max(100)->default(15),
            'page' => $schema->integer()->min(1)->default(1),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->tokenCan('subscribers.manage')) {
            return $this->missingAbility('subscribers.manage');
        }

        $subscribers = Subscriber::query()
            ->with('components')
            ->when($request->filled('email'), fn ($query) => $query->where('email', 'like', '%'.$request->get('email').'%'))
            ->when($request->filled('global'), fn ($query) => $query->where('global', $request->boolean('global')))
            ->simplePaginate(perPage: $this->perPage($request), page: $this->page($request));

        return Response::structured([
            'data' => $subscribers->getCollection()->map(fn (Subscriber $subscriber) => $this->presentSubscriber($subscriber))->all(),
            'meta' => $this->paginationMeta($subscribers),
        ]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('subscribers.manage');
    }
}
