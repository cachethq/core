<?php

return [
    'resource_label' => 'Subscriber|Subscribers',
    'list' => [
        'headers' => [
            'email' => 'Email',
            'verify_code' => 'Verify code',
            'global' => 'Global',
            'phone_number' => 'Phone number',
            'slack_webhook_url' => 'Slack Webhook URL',
            'verified_at' => 'Verified at',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ],
        'empty_state' => [
            'heading' => 'Subscribers',
            'description' => 'Subscribers are people who have subscribed to your status page for notifications.',
        ],
        'actions' => [
            'verify_label' => 'Verify',
        ],
    ],
    'form' => [
        'email_label' => 'Email',
        'verify_code_label' => 'Verify code',
        'verified_at_label' => 'Verified at',
        'global_label' => 'Global',
    ],
    'overview' => [
        'total_subscribers_label' => 'Total Subscribers',
        'total_subscribers_description' => 'Total number of subscribers.',
    ],
];
