<?php

return [
    'resource_label' => 'Template ng Insidente|Mga Template ng Insidente',
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'slug' => 'Slug',
            'engine' => 'Engine',
            'created_at' => 'Ginawa Noong',
            'updated_at' => 'Na-update Noong',
            'deleted_at' => 'Nabura Noong',
        ],
        'empty_state' => [
            'heading' => 'Mga Template ng Insidente',
            'description' => 'Ginagamit ang mga template ng insidente para gumawa ng reusable na mga mensahe ng insidente.',
        ],
    ],
    'form' => [
        'name_label' => 'Pangalan',
        'slug_label' => 'Slug',
        'template_label' => 'Template',
        'engine_label' => 'Engine',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Dokumentasyon ng Laravel Blade',
        'twig' => 'Twig',
        'twig_docs' => 'Dokumentasyon ng Twig',
    ],
];
