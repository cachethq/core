<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers\Subscribers;

use Cachet\Data\Requests\Subscriber\VerifySubscriberEmailRequestData;
use Illuminate\Http\RedirectResponse;

class VerifySubscriberEmailController
{
    public function __invoke(VerifySubscriberEmailRequestData $request): RedirectResponse
    {
        $request->fulfil();

        session()->flash('success', __('cachet::subscriber.messages.email_verified'));

        return redirect()->route('cachet.status-page');
    }
}
