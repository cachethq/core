<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Incident\CreateIncidentData;
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
    public function handle(CreateIncidentData $data): Incident
    {
        if (isset($data->template)) {
            $template = IncidentTemplate::query()
                ->where('slug', $data->template)
                ->first();

            $data = $data->withMessage($this->parseTemplate($template, $data));
        }

        // @todo Dispatch notification that incident was created.

        return Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $data->toArray()
        ));
    }

    /**
     * Render the incident template with the given data.
     */
    private function parseTemplate(IncidentTemplate $template, CreateIncidentData $data): string
    {
        $vars = array_merge($data->templateVars, [
            'incident' => [
                'name' => $data->name,
                'status' => $data->status,
                'message' => $data->message ?? null,
                'visible' => $data->visible ?? false,
                'notify' => $data->notifications ?? false,
                'stickied' => $data->stickied ?? false,
                'occurred_at' => $data->occurredAt ?? Carbon::now(),
                'component' => $data->componentId ? Component::find($data->componentId) : null,
                'component_status' => $data->componentStatus ?? null,
            ],
        ]);

        return $template->render($vars);
    }
}
