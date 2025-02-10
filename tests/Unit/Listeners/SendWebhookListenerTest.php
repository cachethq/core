<?php

use Cachet\Actions\Webhook\DispatchWebhooks;
use Cachet\Concerns\SendsWebhook;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Listeners\SendWebhookListener;

use function Pest\Laravel\mock;

it('sends when the event uses SendsWebhook', function () {
    $dispatchWebhooks = mock(DispatchWebhooks::class)->makePartial();

    $event = new class
    {
        use SendsWebhook;

        public function getWebhookPayload(): array
        {
            return [];
        }

        public function getWebhookEventName(): WebhookEventEnum
        {
            return WebhookEventEnum::component_updated;
        }
    };

    app(SendWebhookListener::class)->handle($event::class, [$event]);

    $dispatchWebhooks->shouldHaveReceived('handle')->with($event);
});

it('will not send if the event doesn\'t implement the SendsWebhook trait', function () {
    $dispatchWebhooks = mock(DispatchWebhooks::class);

    $event = new class {};

    app(SendWebhookListener::class)->handle($event::class, [$event]);

    $dispatchWebhooks->shouldNotHaveReceived('handle');
});
