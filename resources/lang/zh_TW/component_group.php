<?php

return [
    'resource_label' => '組件組|組件組',
    'incident_count' => ':count 事件|:count 事件',
    'visibility' => [
        'expanded' => '始終展開',
        'collapsed' => '始終摺疊',
        'collapsed_unless_incident' => '沒有正在進行中的事件時摺疊',
    ],
    'list' => [
        'headers' => [
            'name' => '名稱',
            'visible' => '可見',
            'collapsed' => '摺疊',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
        ],
        'empty_state' => [
            'heading' => '組件組',
            'description' => '將相關組件分組。',
        ],
    ],
    'form' => [
        'name_label' => '名稱',
        'visible_label' => '可見',
        'collapsed_label' => '摺疊',
    ],
];
