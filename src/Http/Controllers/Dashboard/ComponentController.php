<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers\Dashboard;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Http\Requests\CreateComponentRequest;
use Cachet\Models\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Response;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return inertia('Dashboard/Components/Index', [
            'components' => fn () => Component::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return inertia('Dashboard/Components/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateComponentRequest $request): RedirectResponse
    {
        CreateComponent::run(
            name: $request->input('name'),
            description: $request->input('description'),
            link: $request->input('link'),
        );

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Component $component): Response
    {
        return inertia('Dashboard/Components/Show', [
            'component' => $component,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Component $component): Response
    {
        return inertia('Dashboard/Components/Edit', [
            'component' => $component,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Component $component)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Component $component): RedirectResponse
    {
        DeleteComponent::run($component);

        return redirect()->back();
    }
}
