<?php

return [
    'resource_label' => 'Incident Template|Incident Templates',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'slug' => 'Slug',
            'engine' => 'Engine',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'deleted_at' => 'Deleted at',
        ],
        'empty_state' => [
            'heading' => 'Incident Templates',
            'description' => 'Incident templates are used to create reusable incident messages.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'slug_label' => 'Slug',
        'template_label' => 'Template',
        'engine_label' => 'Engine',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade Documentation',
        'twig' => 'Twig',
        'twig_docs' => 'Twig Documentation',
    ],
];
