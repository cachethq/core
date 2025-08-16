<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'verify_code' => Str::random(42),
        ];
    }

    public function verified(): self
    {
        return $this->state([
            'verified_at' => now(),
        ]);
    }
}
