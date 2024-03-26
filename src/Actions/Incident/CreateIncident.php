<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CreateIncident
{
    public function handle(array $incident): Incident
    {
        if (isset($incident['template'])) {
            $template = IncidentTemplate::where('slug', $incident['template'])->first();
            $incident['message'] = $this->parseTemplate($template, $incident);
        }

        return Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $incident
        ));
    }

    /**
     * Render the incident template with the given data.
     */
    private function parseTemplate(IncidentTemplate $template, array $data): string
    {
        $vars = array_merge($data['template_vars'], [
            'incident' => [
                'name' => $data['name'],
                'status' => $data['status'],
                'message' => $data['message'] ?? null,
                'visible' => $data['visible'] ?? false,
                'notify' => $data['notifications'] ?? false,
                'stickied' => $data['stickied'] ?? false,
                'occurred_at' => $data['occurred_at'] ?? Carbon::now(),
                'component' => isset($data['component_id']) ? Component::find($data['component_id']) : null,
                'component_status' => $data['component_status'] ?? null,
            ],
        ]);

        return $template->render($vars);
    }
}
