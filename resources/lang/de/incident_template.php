<?php

return [
    'resource_label' => 'Vorfallvorlage|Vorfallvorlagen',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'slug' => 'Schlagwort',
            'engine' => 'Methode',
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
        'slug_label' => 'Schlagwort',
        'template_label' => 'Vorlage',
        'engine_label' => 'Methode',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade Dokumentation',
        'twig' => 'Twig',
        'twig_docs' => 'Twig Dokumentation',
    ],
];
