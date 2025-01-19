<?php

return [
    'resource_label' => '事件模板|事件模板',
    'list' => [
        'headers' => [
            'name' => '名稱',
            'slug' => '別名',
            'engine' => '引擎',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
            'deleted_at' => '刪除時間',
        ],
        'empty_state' => [
            'heading' => '事件模板',
            'description' => '事件模板用於創建可重複使用的事件消息。',
        ],
    ],
    'form' => [
        'name_label' => '名稱',
        'slug_label' => '別名',
        'template_label' => '模板',
        'engine_label' => '引擎',
    ],
    'engine' => [
        'laravel_blade' => 'Laravel Blade',
        'laravel_blade_docs' => 'Laravel Blade 文檔',
        'twig' => 'Twig',
        'twig_docs' => 'Twig 文檔',
    ],
];
