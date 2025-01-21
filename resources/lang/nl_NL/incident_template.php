<?php

return [
    'resource_label' => 'Incidenten sjabloon|Incidenten sjablonen',
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'slug' => 'Trefwoord',
            'engine' => 'Methode',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
        ],
        'empty_state' => [
            'heading' => 'Incidentsjablonen',
            'description' => 'Incidentsjablonen worden gebruikt om herbruikbare incidentrapporten te maken.',
        ],
    ],
    'form' => [
        'name_label' => 'Naam',
        'slug_label' => 'Trefwoord',
        'template_label' => 'Sjabloon',
        'engine_label' => 'Methode',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade Documentatie',
        'twig' => 'Twig',
        'twig_docs' => 'Twig Documentatie',
    ],
];
