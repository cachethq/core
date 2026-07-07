<?php

namespace Cachet\Http\Controllers\Subscribers;

use Cachet\Actions\Subscriber\CreateSubscriber;
use Cachet\Settings\MailSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriberController
{
    /**
     * Show the subscribe page.
     */
    public function create(MailSettings $mailSettings): View
    {
        abort_unless($mailSettings->allow_subscribers || session()->has('cachet_subscriber_status'), 404);

        return view('cachet::status-page.subscribe');
    }

    /**
     * Subscribe to status page updates.
     */
    public function store(Request $request, MailSettings $mailSettings, CreateSubscriber $createSubscriber): RedirectResponse
    {
        abort_unless($mailSettings->allow_subscribers, 404);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = $createSubscriber->handle($validated['email']);

        if (! $subscriber->hasVerifiedEmail()) {
            $subscriber->sendEmailVerificationNotification();
        }

        return redirect()
            ->route('cachet.subscribers.create')
            ->with('cachet_subscriber_status', 'subscribed');
    }
}
