<?php

namespace Cachet\Http\Controllers\Subscription;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

final class SubscriptionController
{
    public function create(): Response
    {
        return response()->view('cachet::pages.subscriptions.create');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('cachet.status-page');
    }
}
