<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers\Dashboard;

use Cachet\Models\Component;
use Illuminate\Routing\Controller;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return inertia('Dashboard/Index', [
            'components' => fn () => Component::all(),
        ]);
    }
}
