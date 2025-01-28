<?php

namespace Cachet\Http\Controllers\Subscribers;

use Illuminate\Contracts\View\View;

final class SubscriberController
{
    public function create(): View
    {
        return view('cachet::pages.subscribers.create');
    }
}
