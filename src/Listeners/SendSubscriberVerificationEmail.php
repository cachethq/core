<?php

namespace Cachet\Listeners;

use Cachet\Events\Subscribers\SubscriberCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSubscriberVerificationEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubscriberCreated $event): void
    {
        if ($event->subscriber->hasVerifiedEmail()) {
            return;
        }

        $event->subscriber->sendEmailVerificationNotification();
    }

    /**
     * Get the name of the listener's queue connection.
     */
    public function viaConnection(): string
    {
        return config('cachet.subscribers.queue_connection');
    }

    /**
     * Get the name of the listener's queue.
     */
    public function viaQueue(): string
    {
        return config('cachet.subscribers.queue_name');
    }
}
