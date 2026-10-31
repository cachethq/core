<?php

return [
    'resource_label' => 'Vorfallvorlage|Vorfallvorlagen',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'slug' => 'Slug',
            'engine' => 'Template-Engine',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'empty_state' => [
            'heading' => 'Vorfallvorlage',
            'description' => 'Vorfallvorlagen werden zum Erstellen wiederverwendbarer Vorfallmeldungen verwendet.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'slug_label' => 'Slug',
        'template_label' => 'Vorlage',
        'engine_label' => 'Template-Engine',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Dokumentation zu Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Twig-Dokumentation',
    ],
];
