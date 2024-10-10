<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate;
use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate;
use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Http\Requests\CreateIncidentTemplateRequest;
use Cachet\Http\Requests\UpdateIncidentTemplateRequest;
use Cachet\Http\Resources\IncidentTemplate as IncidentTemplateResource;
use Cachet\Models\IncidentTemplate;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class IncidentTemplateController extends Controller
{
    /**
     * List Incident Templates.
     */
    public function index()
    {
        $templates = QueryBuilder::for(IncidentTemplate::class)
            ->allowedFilters(['name', 'slug'])
            ->allowedSorts(['name', 'slug', 'id'])
            ->simplePaginate(request('per_page', 15));

        return IncidentTemplateResource::collection($templates);
    }

    /**
     * Create Incident Template.
     */
    public function store(CreateIncidentTemplateRequest $request)
    {
        $template = app(CreateIncidentTemplate::class)->handle($request->validated());

        return IncidentTemplateResource::make($template);
    }

    /**
     * Get Incident Template.
     */
    public function show(IncidentTemplate $incidentTemplate)
    {
        return IncidentTemplateResource::make($incidentTemplate)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Template.
     */
    public function update(UpdateIncidentTemplateRequest $request, IncidentTemplate $incidentTemplate)
    {
        app(UpdateIncidentTemplate::class)->handle($incidentTemplate, $request->validated());

        return IncidentTemplateResource::make($incidentTemplate->fresh());
    }

    /**
     * Delete Incident Template.
     */
    public function destroy(IncidentTemplate $incidentTemplate)
    {
        app(DeleteIncidentTemplate::class)->handle($incidentTemplate);

        return response()->noContent();
    }
}
