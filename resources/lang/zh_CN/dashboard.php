<?php

return [
    'system_health' => [
        'empty' => '尚未配置任何组件。',
        'add_component' => '添加组件',
    ],
    'open_incidents' => [
        'heading' => '未解决的事件',
        'headers' => [
            'name' => '名称',
            'status' => '状态',
            'occurred_at' => '发生时间',
            'components' => '受影响的组件',
        ],
        'actions' => [
            'create' => '创建事件',
        ],
        'empty_state' => [
            'heading' => '没有未解决的事件',
            'description' => '一切正常。尚未修复的事件将显示在这里。',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => '维护',
        'headers' => [
            'name' => '名称',
            'status' => '状态',
            'scheduled_at' => '预定时间',
        ],
        'actions' => [
            'create' => '安排维护',
        ],
        'empty_state' => [
            'heading' => '没有计划的维护',
            'description' => '即将进行和进行中的维护将显示在这里。',
        ],
    ],
];
