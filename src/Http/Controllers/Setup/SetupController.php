<?php

namespace Cachet\Http\Controllers\Setup;

use Illuminate\Routing\Controller;

class SetupController extends Controller
{
    public function index()
    {
        return view('cachet::setup.index', [
            'title' => __('Setup Cachet'),
        ]);
    }
}
