<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Models\Subscriber;

class UnsubscribeSubscriber
{
    /**
     * Handle the action.
     */
    public function handle(Subscriber $subscriber): void
    {
        $subscriber->components()->delete();

        SubscriberUnsubscribed::dispatch($subscriber);

        $subscriber->delete();
    }
}
