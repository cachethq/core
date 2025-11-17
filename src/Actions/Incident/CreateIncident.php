<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Data\Requests\Incident\IncidentComponentRequestData;
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

        return tap(Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $data->except('components')->toArray()
        )), function (Incident $incident) use ($data) {
            if (! $data->components) {
                return;
            }

            $components = collect($data->components)->map(fn (IncidentComponentRequestData $component) => [
                'component_id' => $component->id,
                'component_status' => $component->status,
            ])->all();

            $incident->components()->sync($components);
        });
    }

    /**
     * Render the incident template with the given data.
     */
    private function parseTemplate(IncidentTemplate $template, CreateIncidentRequestData $data): string
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
