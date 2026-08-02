<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\DB;

class UpdateSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(Subscriber $subscriber, ?string $email = null, ?bool $global = null, ?array $components = null, ?array $meta = null): Subscriber
    {
        return DB::transaction(function () use ($subscriber, $email, $global, $components, $meta): Subscriber {
            $subscriber->update(array_filter([
                'email' => $email,
                'global' => $global,
            ], fn ($value) => $value !== null));

            if ($subscriber->wasChanged('email')) {
                $subscriber->resetVerification();
            }

            if ($components !== null) {
                $subscriber->components()->sync($components);
            }

            if ($meta !== null) {
                $subscriber->syncMeta($meta);
            }

            return $subscriber;
        });
    }
}
