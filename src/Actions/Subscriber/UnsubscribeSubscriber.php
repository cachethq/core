<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\DB;

class UnsubscribeSubscriber
{
    /**
     * Handle the action.
     *
     * The SubscriberUnsubscribed event is dispatched by the model's deleted event.
     */
    public function handle(Subscriber $subscriber): void
    {
        DB::transaction(function () use ($subscriber): void {
            $subscriber->components()->detach();

            $subscriber->delete();
        });
    }
}
