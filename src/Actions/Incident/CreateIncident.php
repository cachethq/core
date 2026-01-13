<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Data\Requests\Incident\IncidentComponentRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Cachet\Verbs\Events\Incidents\IncidentCreated;
use Illuminate\Support\Carbon;

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

        $components = [];
        if ($data->components) {
            $components = collect($data->components)->map(fn (IncidentComponentRequestData $component) => [
                'id' => $component->id,
                'status' => $component->status,
            ])->all();
        }

        return IncidentCreated::commit(
            name: $data->name,
            status: $data->status ?? IncidentStatusEnum::investigating,
            message: $data->message ?? '',
            visible: $data->visible ? ResourceVisibilityEnum::guest : ResourceVisibilityEnum::authenticated,
            stickied: $data->stickied,
            notifications: $data->notifications,
            occurred_at: $data->occurredAt,
            user_id: auth()->id(),
            components: $components,
        );
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
