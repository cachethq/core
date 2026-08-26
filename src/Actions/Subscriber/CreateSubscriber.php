<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\DB;

class CreateSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(string $email, bool $global = true, array $components = [], bool $verified = false, ?array $meta = null): Subscriber
    {
        return DB::transaction(function () use ($email, $global, $components, $verified, $meta): Subscriber {
            $subscriber = Subscriber::firstOrNew(['email' => $email]);
            $subscriber->fill(['global' => $global]);

            if ($verified && ! $subscriber->hasVerifiedEmail()) {
                $subscriber->email_verified_at = now();
            }

            $subscriber->save();
            $subscriber->components()->syncWithoutDetaching($components);

            if ($meta !== null) {
                $subscriber->syncMeta($meta);
            }

            return $subscriber;
        });
    }
}
