<?php

use Cachet\Actions\IncidentTemplate\CreateIncidentTemplate;
use Cachet\Enums\IncidentTemplateEngineEnum;

it('can create an incident template', function () {
    $incidentTemplate = app(CreateIncidentTemplate::class)->handle([
        'name' => 'GitHub Issues',
        'template' => 'Hey there.',
        'engine' => IncidentTemplateEngineEnum::twig,
    ]);

    expect($incidentTemplate)
        ->slug->toBe('github-issues')
        ->engine->toBe(IncidentTemplateEngineEnum::twig);
});

it('can use a custom slug', function () {
    $incidentTemplate = app(CreateIncidentTemplate::class)->handle([
        'name' => 'GitHub Issues',
        'slug' => 'custom-github-issues',
        'template' => 'Hey there.',
        'engine' => IncidentTemplateEngineEnum::twig,
    ]);

    expect($incidentTemplate)
        ->slug->toBe('custom-github-issues')
        ->engine->toBe(IncidentTemplateEngineEnum::twig);
});
