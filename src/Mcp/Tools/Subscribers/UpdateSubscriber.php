<?php

namespace Cachet\Mcp\Tools\Subscribers;

use Cachet\Actions\Subscriber\UpdateSubscriber as UpdateSubscriberAction;
use Cachet\Data\Requests\Subscriber\UpdateSubscriberRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Cachet\Models\Subscriber;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
class UpdateSubscriber extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'update_subscriber';

    protected string $description = 'Update an existing subscriber\'s email, global state, or component subscriptions. Requires the subscribers.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The subscriber ID.'),
            'email' => $schema->string()->max(255)->description('The email address of the subscriber.'),
            'global' => $schema->boolean()->description('Whether the subscriber receives notifications for all components.'),
            'components' => $schema->array()
                ->items($schema->integer())
                ->description('IDs of components the subscriber is subscribed to.'),
        ];
    }

    public function handle(Request $request, UpdateSubscriberAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCanAnd('subscribers.manage', 'viewAny', Subscriber::class)) {
            return $this->missingAbility('subscribers.manage');
        }

        $subscriber = Subscriber::query()->find($id = $request->integer('id'));

        if ($subscriber === null) {
            return Response::error("Subscriber [{$id}] not found.");
        }

        if (! $this->tokenCanAnd('subscribers.manage', 'update', $subscriber)) {
            return $this->missingAbility('subscribers.manage');
        }

        $data = UpdateSubscriberRequestData::validateAndCreate($request->only(['email', 'global', 'components']));

        $action->handle($subscriber, email: $data->email, global: $data->global, components: $data->components);

        return Response::structured(['data' => $this->presentSubscriber($subscriber->fresh())]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCanAnd('subscribers.manage', 'viewAny', Subscriber::class);
    }
}
