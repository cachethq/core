<?php

use Cachet\Models\IncidentTemplate;
use Twig\Sandbox\SecurityError;

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

it('renders allowed twig filters', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'template' => 'Hello, {{ name|upper }}!',
    ]);

    $output = $template->render([
        'name' => 'James Brooks',
    ]);

    expect($output)
        ->toBe('Hello, JAMES BROOKS!');
});

it('sandboxes twig templates to block code execution', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'template' => "{{ ['id']|map('system')|join }}",
    ]);

    expect(fn () => $template->render())
        ->toThrow(SecurityError::class);
});

it('blocks disallowed twig filters', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'template' => "{{ ['whoami']|filter('system')|join }}",
    ]);

    expect(fn () => $template->render())
        ->toThrow(SecurityError::class);
});
