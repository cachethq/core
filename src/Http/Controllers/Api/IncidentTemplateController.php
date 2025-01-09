<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate;
use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate;
use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Data\Requests\IncidentTemplate\CreateIncidentTemplateRequestData;
use Cachet\Data\Requests\IncidentTemplate\UpdateIncidentTemplateRequestData;
use Cachet\Http\Resources\IncidentTemplate as IncidentTemplateResource;
use Cachet\Models\IncidentTemplate;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Incident Templates', weight: 5)]
class IncidentTemplateController extends Controller
{
    /**
     * List Incident Templates
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, slug, id. Example: name
     * @queryParam filters[name] string Filter by name. Example: My Template
     * @queryParam filters[slug] string Filter by slug. Example: my-template
     * @queryParam filters[id] int Filter by id. Example: 1
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
     * Create Incident Template
     */
    public function store(CreateIncidentTemplateRequestData $data, CreateIncidentTemplate $createIncidentTemplateAction)
    {
        $template = $createIncidentTemplateAction->handle($data);

        return IncidentTemplateResource::make($template);
    }

    /**
     * Get Incident Template
     */
    public function show(IncidentTemplate $incidentTemplate)
    {
        return IncidentTemplateResource::make($incidentTemplate)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Template
     */
    public function update(UpdateIncidentTemplateRequestData $data, IncidentTemplate $incidentTemplate, UpdateIncidentTemplate $updateIncidentTemplateAction)
    {
        $template = $updateIncidentTemplateAction->handle($incidentTemplate, $data);

        return IncidentTemplateResource::make($template);
    }

    /**
     * Delete Incident Template
     */
    public function destroy(IncidentTemplate $incidentTemplate)
    {
        app(DeleteIncidentTemplate::class)->handle($incidentTemplate);

        return response()->noContent();
    }
}
