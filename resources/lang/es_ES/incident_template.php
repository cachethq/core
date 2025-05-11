<?php

return [
    'resource_label' => 'Plantilla de Incidente|Plantillas de Incidente',
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'slug' => 'Slug',
            'engine' => 'Motor',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
            'deleted_at' => 'Eliminado el',
        ],
        'empty_state' => [
            'heading' => 'Plantillas de Incidente',
            'description' => 'Las plantillas de incidente se utilizan para crear mensajes de incidente reutilizables.',
        ],
    ],
    'form' => [
        'name_label' => 'Nombre',
        'slug_label' => 'Slug',
        'template_label' => 'Plantilla',
        'engine_label' => 'Motor',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Documentación de Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Documentación de Twig',
    ],
];
