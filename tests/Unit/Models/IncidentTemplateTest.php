<?php

use Cachet\Models\IncidentTemplate;

// @todo allow for a template slug to be automatically generated.
// @todo allow for templates to be rendered with twig (migrate existing templates to have a `engine` field).
// @todo allow for templates to be rendered with blade (set `engine` to `blade` using enum).

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
