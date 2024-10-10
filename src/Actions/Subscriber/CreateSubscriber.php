<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;
use Illuminate\Support\Str;

class CreateSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(string $email, bool $global = false, array $components = [], bool $verified = false): Subscriber
    {
        $subscriber = Subscriber::firstOrCreate([
            'email' => $email,
        ], [
            'global' => $global,
            'verify_code' => Str::random(42),
            'verified_at' => $verified ? now() : null,
        ]);

        $subscriber->components()->attach($components);

        return $subscriber;
    }
}
