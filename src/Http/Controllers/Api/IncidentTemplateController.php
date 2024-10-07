<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate;
use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate;
use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Data\IncidentTemplate\CreateIncidentTemplateData;
use Cachet\Data\IncidentTemplate\UpdateIncidentTemplateData;
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
    public function store(CreateIncidentTemplateData $data, CreateIncidentTemplate $createIncidentTemplateAction)
    {
        $template = $createIncidentTemplateAction->handle($data);

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
    public function update(UpdateIncidentTemplateData $data, IncidentTemplate $incidentTemplate, UpdateIncidentTemplate $updateIncidentTemplateAction)
    {
        $template = $updateIncidentTemplateAction->handle($incidentTemplate, $data);

        return IncidentTemplateResource::make($template);
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
