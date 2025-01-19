<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => '發送所有事件',
        'selected' => '僅發送選定事件',
    ],
    'form' => [
        'url_label' => '有效載荷 URL',
        'url_helper' => '事件將通過此 URL 發送 POST 請求',
        'secret_label' => '密鑰',
        'secret_helper' => '有效載荷將使用此密鑰進行簽名。更多信息請查看*web Hook 文檔*。',
        'description_label' => '描述',
        'event_selection_label' => '發送所有事件？',
        'events_label' => '事件',
        'edit_secret_label' => '編輯密鑰',
        'update_secret_label' => '更新密鑰',
    ],
    'attempts' => [
        'heading' => '嘗試',
        'empty_state' => '尚未對此 webhook 進行過任何嘗試',
    ],
    'list' => [
        'headers' => [
            'url' => '有效載荷 URL',
            'success_rate_24h' => '成功率（24 小時）',
        ],
    ],
];
