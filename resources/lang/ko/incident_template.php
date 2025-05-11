<?php

return [
    'resource_label' => '사고 템플릿|사고 템플릿',
    'list' => [
        'headers' => [
            'name' => '이름',
            'slug' => '슬러그',
            'engine' => '엔진',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
            'deleted_at' => '삭제 시간',
        ],
        'empty_state' => [
            'heading' => '사고 템플릿',
            'description' => '사고 템플릿은 재사용 가능한 사고 메시지를 만드는 데 사용됩니다.',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'slug_label' => '슬러그',
        'template_label' => '템플릿',
        'engine_label' => '엔진',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade 문서',
        'twig' => 'Twig',
        'twig_docs' => 'Twig 문서',
    ],
];
