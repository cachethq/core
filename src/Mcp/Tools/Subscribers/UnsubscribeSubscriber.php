<?php

namespace Cachet\Mcp\Tools\Subscribers;

use Cachet\Actions\Subscriber\UnsubscribeSubscriber as UnsubscribeSubscriberAction;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Models\Subscriber;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[IsDestructive]
class UnsubscribeSubscriber extends Tool
{
    use GuardsMcpAbilities;

    protected string $name = 'unsubscribe_subscriber';

    protected string $description = 'Unsubscribe and remove a subscriber from the status page. Requires the subscribers.delete token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The subscriber ID.'),
        ];
    }

    public function handle(Request $request, UnsubscribeSubscriberAction $action): Response
    {
        if (! $this->tokenCanAnd('subscribers.delete', 'viewAny', Subscriber::class)) {
            return $this->missingAbility('subscribers.delete');
        }

        $subscriber = Subscriber::query()->find($id = $request->integer('id'));

        if ($subscriber === null) {
            return Response::error("Subscriber [{$id}] not found.");
        }

        if (! $this->tokenCanAnd('subscribers.delete', 'delete', $subscriber)) {
            return $this->missingAbility('subscribers.delete');
        }

        $action->handle($subscriber);

        return Response::text("Subscriber [{$id}] unsubscribed.");
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCanAnd('subscribers.delete', 'viewAny', Subscriber::class);
    }
}
