<?php

return [
    'resource_label' => '组件组|组件组',
    'incident_count' => ':count 事件|:count 事件',
    'visibility' => [
        'expanded' => '始终展开',
        'collapsed' => '始终折叠',
        'collapsed_unless_incident' => '没有正在进行中的事件时折叠',
    ],
    'list' => [
        'headers' => [
            'name' => '名称',
            'visible' => '可见',
            'collapsed' => '折叠',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ],
        'empty_state' => [
            'heading' => '组件组',
            'description' => '将相关组件分组。',
        ],
    ],
    'form' => [
        'name_label' => '名称',
        'visible_label' => '可见',
        'collapsed_label' => '折叠',
    ],
];
