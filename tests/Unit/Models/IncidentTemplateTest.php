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

it('can render a blade template', function () {
    config()->set('cachet.renderers.blade', true);

    $template = IncidentTemplate::factory()->blade()->create([
        'template' => 'Hello, {{ $name }}!',
    ]);

    $output = $template->render([
        'name' => 'James Brooks',
    ]);

    expect($output)
        ->toBe('Hello, James Brooks!');
});

it('does not render a blade template when the blade renderer is disabled', function () {
    config()->set('cachet.renderers.blade', false);

    $template = IncidentTemplate::factory()->blade()->create([
        'template' => 'Hello, {{ $name }}!',
    ]);

    expect(fn () => $template->render(['name' => 'James Brooks']))
        ->toThrow(RuntimeException::class);
});

it('does not let a twig template call php functions', function (string $body) {
    $template = IncidentTemplate::factory()->twig()->create([
        'template' => $body,
    ]);

    expect(fn () => $template->render(['name' => 'James Brooks']))
        ->toThrow(SecurityError::class);
})->with([
    'callable filter' => ["{{ ['cachet']|map('strtoupper')|join }}"],
    'template include' => ["{% include 'another-template' %}"],
]);
