<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => '发送所有事件',
        'selected' => '仅发送选定事件',
    ],
    'form' => [
        'url_label' => '有效载荷 URL',
        'url_helper' => '事件将通过此 URL 发送 POST 请求',
        'secret_label' => '密钥',
        'secret_helper' => '有效载荷将使用此密钥进行签名。更多信息请查看*web Hook 文档*。',
        'description_label' => '描述',
        'event_selection_label' => '发送所有事件？',
        'events_label' => '事件',
        'edit_secret_label' => '编辑密钥',
        'update_secret_label' => '更新密钥',
    ],
    'attempts' => [
        'heading' => '尝试',
        'empty_state' => '尚未对此 webhook 进行过任何尝试',
    ],
    'list' => [
        'headers' => [
            'url' => '有效载荷 URL',
            'success_rate_24h' => '成功率（24 小时）',
        ],
    ],
];
