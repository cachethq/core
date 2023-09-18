<?php

use Cachet\Models\IncidentTemplate;

it('can fetch data', function () {
    $template = IncidentTemplate::factory()->create([
        'name' => 'Incident Template',
        'slug' => 'incident-template',
    ]);

    expect($template)->toMatchArray([
        'name' => 'Incident Template',
        'slug' => 'incident-template',
    ])->template->not()->toBeNull();
});

it('can render a twig template', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'template' => 'Hello, {{ name }}!',
    ]);

    $output = $template->render([
        'name' => 'James Brooks',
    ]);

    expect($output)
        ->toBe('Hello, James Brooks!');
});

it('can render a blade template', function () {
    $template = IncidentTemplate::factory()->blade()->create([
        'template' => 'Hello, {{ $name }}!',
    ]);

    $output = $template->render([
        'name' => 'James Brooks',
    ]);

    expect($output)
        ->toBe('Hello, James Brooks!');
});
