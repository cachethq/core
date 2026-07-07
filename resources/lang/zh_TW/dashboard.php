<?php

return [
    'system_health' => [
        'empty' => '尚未配置任何組件。',
        'add_component' => '添加組件',
    ],
    'open_incidents' => [
        'heading' => '未解決的事件',
        'headers' => [
            'name' => '名稱',
            'status' => '狀態',
            'occurred_at' => '發生時間',
            'components' => '受影響的元件',
        ],
        'actions' => [
            'create' => '創建事件',
        ],
        'empty_state' => [
            'heading' => '沒有未解決的事件',
            'description' => '一切正常。尚未修復的事件將顯示在這裡。',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => '維護',
        'headers' => [
            'name' => '名稱',
            'status' => '狀態',
            'scheduled_at' => '預定時間',
        ],
        'actions' => [
            'create' => '安排維護',
        ],
        'empty_state' => [
            'heading' => '沒有計劃的維護',
            'description' => '即將進行和進行中的維護將顯示在這裡。',
        ],
    ],
];
