<?php

return [
    'resource_label' => '事件模板|事件模板',
    'list' => [
        'headers' => [
            'name' => '名称',
            'slug' => '别名',
            'engine' => '引擎',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ],
        'empty_state' => [
            'heading' => '事件模板',
            'description' => '事件模板用于创建可重复使用的事件消息。',
        ],
    ],
    'form' => [
        'name_label' => '名称',
        'slug_label' => '别名',
        'template_label' => '模板',
        'engine_label' => '引擎',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade 文档',
        'twig' => 'Twig',
        'twig_docs' => 'Twig 文档',
    ],
];
