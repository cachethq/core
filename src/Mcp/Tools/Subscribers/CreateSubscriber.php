<?php

namespace Cachet\Mcp\Tools\Subscribers;

use Cachet\Actions\Subscriber\CreateSubscriber as CreateSubscriberAction;
use Cachet\Data\Requests\Subscriber\CreateSubscriberRequestData;
use Cachet\Mcp\Concerns\GuardsMcpAbilities;
use Cachet\Mcp\Concerns\PresentsResources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;

class CreateSubscriber extends Tool
{
    use GuardsMcpAbilities;
    use PresentsResources;

    protected string $name = 'create_subscriber';

    protected string $description = 'Subscribe an email address to status page notifications, either globally or for specific components. Unverified subscribers are sent a verification email. Requires the subscribers.manage token ability.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'email' => $schema->string()->max(255)->required()->description('The email address to subscribe.'),
            'global' => $schema->boolean()->default(true)->description('Whether the subscriber receives notifications for all components.'),
            'components' => $schema->array()
                ->items($schema->integer())
                ->description('IDs of components to subscribe to when not subscribing globally.'),
            'verified' => $schema->boolean()->default(false)->description('Whether to mark the subscriber as verified immediately, skipping the verification email.'),
        ];
    }

    public function handle(Request $request, CreateSubscriberAction $action): Response|ResponseFactory
    {
        if (! $this->tokenCan('subscribers.manage')) {
            return $this->missingAbility('subscribers.manage');
        }

        $data = CreateSubscriberRequestData::validateAndCreate($request->all());

        $subscriber = $action->handle(
            $data->email,
            $data->global ?? true,
            $data->components ?? [],
            $data->verified ?? false,
        );

        if (! $subscriber->hasVerifiedEmail()) {
            $subscriber->sendEmailVerificationNotification();
        }

        return Response::structured(['data' => $this->presentSubscriber($subscriber)]);
    }

    public function shouldRegister(): bool
    {
        return $this->tokenCan('subscribers.manage');
    }
}
