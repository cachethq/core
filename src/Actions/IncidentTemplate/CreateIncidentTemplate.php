<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateIncidentTemplate
{
    use AsAction;

    public function handle(array $template): IncidentTemplate
    {
        $data = array_merge([
            'slug' => Str::slug($template['name']),
        ], $template);

        return IncidentTemplate::create($data);
    }
}
