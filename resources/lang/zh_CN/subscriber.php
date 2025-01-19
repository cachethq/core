<?php

return [
    'resource_label' => '订阅者|订阅者',
    'list' => [
        'headers' => [
            'email' => '电子邮件',
            'verify_code' => '验证码',
            'global' => '全局',
            'phone_number' => '电话号码',
            'slack_webhook_url' => 'Slack Webhook URL',
            'verified_at' => '验证时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ],
        'empty_state' => [
            'heading' => '订阅者',
            'description' => '订阅者是已订阅你的状态页面以接收通知的人员。',
        ],
        'actions' => [
            'verify_label' => '验证',
        ],
    ],
    'form' => [
        'email_label' => '电子邮件',
        'verify_code_label' => '验证码',
        'verified_at_label' => '验证时间',
        'global_label' => '全局',
    ],
    'overview' => [
        'total_subscribers_label' => '总订阅者数',
        'total_subscribers_description' => '订阅者的总数。',
    ],
];
