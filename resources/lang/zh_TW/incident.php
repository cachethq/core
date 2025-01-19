<?php

return [
    'title' => '事件',
    'resource_label' => '事件|事件',
    'status' => [
        'investigating' => '正在調查',
        'identified' => '已確認',
        'watching' => '正在觀察',
        'fixed' => '已修復',
        'reported' => '已報告',
    ],
    'edit_button' => '編輯事件',
    'new_button' => '新建事件',
    'no_incidents_reported' => '沒有事件被報告。',
    'timeline' => [
        'past_incidents_header' => '過去的事件',
        'recent_incidents_header' => '最近的事件',
        'no_incidents_reported_between' => '在 :from 和 :to 之間沒有事件被報告。',
        'navigate' => [
            'previous' => '上一頁',
            'today' => '今天',
            'next' => '下一頁',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => '名稱',
            'status' => '狀態',
            'visible' => '可見',
            'stickied' => '置頂',
            'occurred_at' => '發生時間',
            'notified_subscribers' => '已通知的訂閱者',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
            'deleted_at' => '刪除時間',
        ],
        'actions' => [
            'record_update' => '記錄更新',
            'view_incident' => '查看事件',
        ],
        'empty_state' => [
            'heading' => '事件',
            'description' => '事件用於溝通和跟踪服務的狀態。',
        ],
    ],
    'form' => [
        'name_label' => '名稱',
        'status_label' => '狀態',
        'message_label' => '消息',
        'occurred_at_label' => '發生時間',
        'occurred_at_helper' => '如果留空，將使用事件的創建時間戳。',
        'visible_label' => '可見',
        'user_label' => '用戶',
        'user_helper' => '報告事件的用戶。',
        'notifications_label' => '通知訂閱者？',
        'stickied_label' => '置頂事件？',
        'guid_label' => '事件 UUID',
        'add_component' => [
            'action_label' => '添加組件',
            'header' => '組件',
            'component_label' => '組件',
            'status_label' => '狀態',
        ],
    ],
    'record_update' => [
        'success_title' => '事件 :name 已更新',
        'success_body' => '一個新的事件更新已被記錄。',
        'form' => [
            'message_label' => '消息',
            'status_label' => '狀態',
            'user_label' => '用戶',
            'user_helper' => '誰報告了此事件。',
        ],
    ],
    'overview' => [
        'total_incidents_label' => '總事件數',
        'total_incidents_description' => '事件的總數。',
    ],
];
