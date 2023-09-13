<?php

namespace Cachet\Actions\Subscriber;

use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Models\Subscriber;
use Lorisleiva\Actions\Concerns\AsAction;

class UnsubscribeSubscriber
{
    use AsAction;

    public function handle(Subscriber $subscriber): void
    {
        $subscriber->subscriptions()->delete();

        SubscriberUnsubscribed::dispatch($subscriber);

        $subscriber->delete();
    }
}
