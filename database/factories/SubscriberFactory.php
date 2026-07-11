<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail,
        ];
    }

    public function verified(): self
    {
        return $this->state([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Provide the subscriber with additional meta.
     */
    public function withMeta(): self
    {
        return $this->afterCreating(function (Subscriber $subscriber) {
            $subscriber->meta()->create(['key' => 'foo', 'value' => 'bar']);
        });
    }
}
