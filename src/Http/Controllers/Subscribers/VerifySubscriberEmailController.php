<?php

namespace Cachet\Http\Controllers\Subscribers;

use Cachet\Models\Subscriber;
use Illuminate\Http\RedirectResponse;

class VerifySubscriberEmailController
{
    /**
     * Verify a subscriber's email address.
     */
    public function __invoke(Subscriber $subscriber, string $hash): RedirectResponse
    {
        abort_unless(hash_equals(sha1($subscriber->getEmailForVerification()), $hash), 403);

        $subscriber->verify();

        return redirect()
            ->route('cachet.subscribers.create')
            ->with('cachet_subscriber_status', 'verified');
    }
}
