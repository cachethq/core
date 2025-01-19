<?php

return [
    'resource_label' => 'Template de Incidente|Templates de Incidentes',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'slug' => 'Slug',
            'engine' => 'Engine',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
        ],
        'empty_state' => [
            'heading' => 'Templates de Incidentes',
            'description' => 'Templates de incidentes são usados para criar mensagens de incidentes reutilizáveis.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'slug_label' => 'Slug',
        'template_label' => 'Template',
        'engine_label' => 'Engine',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Documentação do Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Documentação do Twig',
    ],
];
