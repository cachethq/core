<?php

namespace Cachet\Http\Controllers\Dashboard;

use Cachet\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Dashboard/Index/Index', [
            'components' => fn () => Component::all(),
        ]);
    }
}
