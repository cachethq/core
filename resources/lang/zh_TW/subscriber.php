<?php

return [
    'resource_label' => '訂閱者|訂閱者',
    'list' => [
        'headers' => [
            'email' => '電子郵件',
            'verify_code' => '驗證碼',
            'global' => '全局',
            'phone_number' => '電話號碼',
            'slack_webhook_url' => 'Slack Webhook URL',
            'verified_at' => '驗證時間',
            'created_at' => '創建時間',
            'updated_at' => '更新時間',
        ],
        'empty_state' => [
            'heading' => '訂閱者',
            'description' => '訂閱者是已訂閱你的狀態頁面以接收通知的人員。',
        ],
        'actions' => [
            'verify_label' => '驗證',
        ],
    ],
    'form' => [
        'email_label' => '電子郵件',
        'verify_code_label' => '驗證碼',
        'verified_at_label' => '驗證時間',
        'global_label' => '全局',
    ],
    'overview' => [
        'total_subscribers_label' => '總訂閱者數',
        'total_subscribers_description' => '訂閱者的總數。',
    ],
];
