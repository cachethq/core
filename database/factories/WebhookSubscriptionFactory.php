<?php

namespace Cachet\Database\Factories;

use Cachet\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebhookSubscription>
 */
class WebhookSubscriptionFactory extends Factory
{
    protected $model = WebhookSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url,
            'secret' => fake()->randomAscii(),
            'description' => fake()->sentence(),
            'send_all_events' => false,
            'selected_events' => [],
        ];
    }

    /**
     * Create a webhook subscription that is enabled for all events
     */
    public function allEvents(): self
    {
        return $this->state([
            'send_all_events' => true,
        ]);
    }

    /**
     * Create a webhook subscription that is only enabled
     * for the given events.
     */
    public function selectedEvents(array $events): self
    {
        return $this->state([
            'selected_events' => $events,
        ]);
    }
}
