<?php

use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Data\IncidentTemplate\UpdateIncidentTemplateData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Models\IncidentTemplate;

it('can update an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->blade()->create();

    app(UpdateIncidentTemplate::class)->handle($incidentTemplate, UpdateIncidentTemplateData::from([
        'name' => 'GitHub Issues',
        'template' => 'Hey there.',
        'engine' => IncidentTemplateEngineEnum::twig,
    ]));

    expect($incidentTemplate->fresh())
        ->name->toBe('GitHub Issues')
        ->template->toBe('Hey there.')
        ->engine->toBe(IncidentTemplateEngineEnum::twig);
});
