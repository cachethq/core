<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;

class CreateSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(string $email, bool $global = true, array $components = [], bool $verified = false, ?array $meta = null): Subscriber
    {
        $subscriber = Subscriber::firstOrCreate([
            'email' => $email,
        ], [
            'global' => $global,
            'email_verified_at' => $verified ? now() : null,
        ]);

        $subscriber->components()->attach($components);

        if ($meta !== null) {
            $subscriber->syncMeta($meta);
        }

        return $subscriber;
    }
}
