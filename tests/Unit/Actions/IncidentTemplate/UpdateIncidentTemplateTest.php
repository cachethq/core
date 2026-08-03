<?php

use Cachet\Actions\IncidentTemplate\UpdateIncidentTemplate;
use Cachet\Data\Requests\IncidentTemplate\UpdateIncidentTemplateRequestData;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Models\IncidentTemplate;

it('can update an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->blade()->create();

    app(UpdateIncidentTemplate::class)->handle($incidentTemplate, UpdateIncidentTemplateRequestData::from([
        'name' => 'GitHub Issues',
        'template' => 'Hey there.',
        'engine' => IncidentTemplateEngineEnum::twig,
    ]));

    expect($incidentTemplate->fresh())
        ->name->toBe('GitHub Issues')
        ->template->toBe('Hey there.')
        ->engine->toBe(IncidentTemplateEngineEnum::twig);
});

it('keeps the slug when updating only the template body', function () {
    $incidentTemplate = IncidentTemplate::factory()->create(['slug' => 'my-template']);

    app(UpdateIncidentTemplate::class)->handle($incidentTemplate, UpdateIncidentTemplateRequestData::from([
        'template' => 'An updated body.',
    ]));

    expect($incidentTemplate->fresh())
        ->template->toBe('An updated body.')
        ->slug->toBe('my-template');
});

it('regenerates the slug when the name changes', function () {
    $incidentTemplate = IncidentTemplate::factory()->create(['slug' => 'old-slug']);

    app(UpdateIncidentTemplate::class)->handle($incidentTemplate, UpdateIncidentTemplateRequestData::from([
        'name' => 'New Name',
    ]));

    expect($incidentTemplate->fresh())->slug->toBe('new-name');
});

it('prefers an explicit slug over one derived from the name', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    app(UpdateIncidentTemplate::class)->handle($incidentTemplate, UpdateIncidentTemplateRequestData::from([
        'name' => 'New Name',
        'slug' => 'explicit-slug',
    ]));

    expect($incidentTemplate->fresh())->slug->toBe('explicit-slug');
});
