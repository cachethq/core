<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;
use Illuminate\Support\Str;

class CreateIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(array $template): IncidentTemplate
    {
        $data = array_merge([
            'slug' => Str::slug($template['name']),
        ], $template);

        return IncidentTemplate::create($data);
    }
}
