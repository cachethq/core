<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;

class UpdateSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(Subscriber $subscriber, ?string $email = null, bool $global = false, array $components = []): Subscriber
    {
        $subscriber->update(array_filter([
            'email' => $email,
            'global' => $global,
        ], fn ($value) => $value !== null));

        if ($subscriber->wasChanged('email')) {
            $subscriber->resetVerification();
        }

        $subscriber->components()->sync($components);

        return $subscriber;
    }
}
