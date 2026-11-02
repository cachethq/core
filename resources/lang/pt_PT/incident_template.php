<?php

return [
    'resource_label' => 'Modelo de incidente|Modelos de incidente',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'slug' => 'Slug',
            'engine' => 'Motor',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Eliminado em',
        ],
        'empty_state' => [
            'heading' => 'Modelos de incidente',
            'description' => 'Os modelos de incidente servem para criar mensagens de incidente reutilizáveis.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'slug_label' => 'Slug',
        'template_label' => 'Modelo',
        'engine_label' => 'Motor',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Documentação do Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Documentação do Twig',
    ],
];
