<?php

return [
    'resource_label' => '組件|組件',
    'list' => [
        'headers' => [
            'name' => '名稱',
            'status' => '狀態',
            'order' => '順序',
            'group' => '組',
            'enabled' => '啟用',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
            'deleted_at' => '刪除時間',
        ],
        'empty_state' => [
            'heading' => '組件',
            'description' => '組件代表系統中可能影響狀態頁面狀態的各種部分。',
        ],
    ],
    'last_updated' => '上次更新 :timestamp',
    'view_details' => '查看詳情',
    'form' => [
        'name_label' => '名稱',
        'status_label' => '狀態',
        'description_label' => '描述',
        'component_group_label' => '組件組',
        'link_label' => '鏈接',
        'link_helper' => '可選的組件鏈接。',
    ],
    'status' => [
        'operational' => '正常運行',
        'performance_issues' => '性能問題',
        'partial_outage' => '部分故障',
        'major_outage' => '重大故障',
        'unknown' => '未知',
    ],

];
