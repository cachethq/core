<?php

namespace Cachet\Http\Controllers\Setup;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class SetupController extends Controller
{
    public function index()
    {
        return Inertia::render('Setup/Index');
    }
}
