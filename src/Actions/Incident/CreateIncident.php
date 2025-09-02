<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CreateIncident
{
    /**
     * Handle the action.
     */
    public function handle(CreateIncidentRequestData $data): Incident
    {
        if (isset($data->template)) {
            $template = IncidentTemplate::query()
                ->where('slug', $data->template)
                ->first();

            $data = $data->withMessage($this->parseTemplate($template, $data));
        }

        // @todo Dispatch notification that incident was created.

        $incident = Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $data->toArray()
        ));

        if (!empty($data->components)) {
            $this->associateComponents($incident, $data->components);
        }

        return $incident;
    }

    /**
     * Render the incident template with the given data.
     */
    private function parseTemplate(IncidentTemplate $template, CreateIncidentRequestData $data): string
    {
        $firstComponent = !empty($data->components) ? $data->components[0] : null;

        $vars = array_merge($data->templateVars, [
            'incident' => [
                'name' => $data->name,
                'status' => $data->status,
                'message' => $data->message ?? null,
                'visible' => $data->visible ?? false,
                'notify' => $data->notifications ?? false,
                'stickied' => $data->stickied ?? false,
                'occurred_at' => $data->occurredAt ?? Carbon::now(),
                'component' => $firstComponent ? Component::find($firstComponent['component_id']) : null,
                'component_status' => $firstComponent ? ComponentStatusEnum::from($firstComponent['component_status']) : null,
                'components' => collect($data->components)->map(function ($comp) {
                    return [
                        'component' => Component::find($comp['component_id']),
                        'status' => ComponentStatusEnum::from($comp['component_status']),
                    ];
                })->toArray(),
            ],
        ]);

        return $template->render($vars);
    }

    /**
     * Associate components with the incident.
     */
    private function associateComponents(Incident $incident, array $components): void
    {
        foreach ($components as $componentData) {
            $componentId = $componentData['component_id'];
            $componentStatus = ComponentStatusEnum::from($componentData['component_status']);

            $incident->components()->attach($componentId, ['component_status' => $componentStatus->value]);

            Component::query()->find($componentId)?->update(['status' => $componentStatus->value]);
        }
    }
}
