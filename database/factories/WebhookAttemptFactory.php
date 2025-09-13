<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\WebhookAttempt;
use Cachet\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebhookAttempt>
 */
class WebhookAttemptFactory extends Factory
{
    protected $model = WebhookAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => WebhookSubscription::factory(),
            'event' => WebhookEventEnum::component_created,
            'attempt' => 0,
            'payload' => [],
            'response_code' => 200,
            'transfer_time' => 0.1,
        ];
    }
}
