<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate;
use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate;
use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\IncidentTemplate\CreateIncidentTemplateRequestData;
use Cachet\Data\Requests\IncidentTemplate\UpdateIncidentTemplateRequestData;
use Cachet\Http\Resources\IncidentTemplate as IncidentTemplateResource;
use Cachet\Models\IncidentTemplate;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Incident Templates', weight: 5)]
class IncidentTemplateController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Incident Templates
     */
    #[QueryParameter('filter[name]', 'Filter by name', example: 'My Template')]
    #[QueryParameter('filter[slug]', 'Filter by slug', example: 'my-template')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
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
        $this->guard('incident-templates.manage');

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
        $this->guard('incident-templates.manage');

        $template = $updateIncidentTemplateAction->handle($incidentTemplate, $data);

        return IncidentTemplateResource::make($template);
    }

    /**
     * Delete Incident Template
     */
    public function destroy(IncidentTemplate $incidentTemplate)
    {
        $this->guard('incident-templates.delete');

        app(DeleteIncidentTemplate::class)->handle($incidentTemplate);

        return response()->noContent();
    }
}
