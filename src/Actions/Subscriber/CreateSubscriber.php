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
            $subscriber = Subscriber::firstOrCreate(['email' => $email], [
                'global' => $global,
                'email_verified_at' => $verified ? now() : null,
            ]);

            $subscriber->fill(['global' => $global]);

            if ($subscriber->isDirty('global')) {
                $subscriber->save();
            }

            if ($verified) {
                $subscriber->verify();
            }

            $subscriber->components()->syncWithoutDetaching($components);

            if ($meta !== null) {
                $subscriber->syncMeta($meta);
            }

            return $subscriber;
        });
    }
}
