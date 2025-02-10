<?php

return [
    'resource_label' => '計劃|計劃',
    'list' => [
        'headers' => [
            'name' => '名稱',
            'status' => '狀態',
            'scheduled_at' => '預定時間',
            'completed_at' => '完成時間',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
            'deleted_at' => '刪除時間',
        ],
        'empty_state' => [
            'heading' => '計劃安排',
            'description' => '規劃和安排你的維護工作。',
        ],
        'actions' => [
            'record_update' => '記錄更新',
            'complete' => '完成維護',
        ],
    ],
    'form' => [
        'name_label' => '名稱',
        'message_label' => '消息',
        'scheduled_at_label' => '預定時間',
        'completed_at_label' => '完成時間',
    ],
    'add_update' => [
        'success_title' => '計劃 :name 已更新',
        'success_body' => '已記錄新的計劃更新。',
        'form' => [
            'message_label' => '消息',
            'completed_at_label' => '完成時間',
        ],
    ],
    'status' => [
        'upcoming' => '即將進行',
        'in_progress' => '進行中',
        'complete' => '完成',
    ],
    'planned_maintenance_header' => '計劃維護',
];
