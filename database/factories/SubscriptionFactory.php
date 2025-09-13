<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Component;
use Cachet\Models\Subscriber;
use Cachet\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'subscriber_id' => Subscriber::factory(),
            'component_id' => Component::factory(),
        ];
    }
}
