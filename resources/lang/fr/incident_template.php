<?php

return [
    'resource_label' => 'Modèle d’incident|Modèles d’incident',
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'slug' => 'Slug',
            'engine' => 'Moteur',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
            'deleted_at' => 'Supprimé le',
        ],
        'empty_state' => [
            'heading' => 'Modèles d’incident',
            'description' => 'Les modèles d’incident sont utilisés pour créer des messages d’incident réutilisables.',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'slug_label' => 'Slug',
        'template_label' => 'Modèle',
        'engine_label' => 'Moteur',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Documentation Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Documentation Twig',
    ],
];
