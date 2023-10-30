<?php

namespace Cachet\Http\Controllers\Dashboard;

use Inertia\Response;

class KitchenSinkController
{
    public function index(): Response
    {
        return inertia('Dashboard/KitchenSink');
    }
}
