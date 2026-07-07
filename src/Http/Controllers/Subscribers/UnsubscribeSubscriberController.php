<?php

namespace Cachet\Http\Controllers\Subscribers;

use Cachet\Actions\Subscriber\UnsubscribeSubscriber;
use Cachet\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UnsubscribeSubscriberController
{
    /**
     * Show the unsubscribe confirmation page.
     */
    public function confirm(Subscriber $subscriber, string $hash): View
    {
        abort_unless(hash_equals(sha1($subscriber->email), $hash), 403);

        return view('cachet::status-page.unsubscribe', [
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Unsubscribe a subscriber from status page updates.
     */
    public function destroy(Subscriber $subscriber, string $hash, UnsubscribeSubscriber $unsubscribeSubscriber): RedirectResponse
    {
        abort_unless(hash_equals(sha1($subscriber->email), $hash), 403);

        $unsubscribeSubscriber->handle($subscriber);

        return redirect()
            ->route('cachet.subscribers.create')
            ->with('cachet_subscriber_status', 'unsubscribed');
    }
}
