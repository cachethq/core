<?php

return [
    'resource_label' => 'Subscriber|Subscribers',
    'list' => [
        'headers' => [
            'email' => 'Email',
            'verify_code' => 'Code ng Pagkumpirma',
            'global' => 'Pangkalahatan',
            'phone_number' => 'Numero ng Telepono',
            'slack_webhook_url' => 'Slack Webhook URL',
            'verified_at' => 'Nakumpirma noong',
            'created_at' => 'Nilikha noong',
            'updated_at' => 'Na-update noong',
        ],
        'empty_state' => [
            'heading' => 'Mga Subscriber',
            'description' => 'Ang mga subscriber ay mga tao na nag-subscribe sa iyong status na pahina para sa mga abiso.',
        ],
        'actions' => [
            'verify_label' => 'Kumpirmahin',
        ],
    ],
    'form' => [
        'email_label' => 'Email',
        'verify_code_label' => 'Code ng Pagkumpirma',
        'verified_at_label' => 'Nakumpirma noong',
        'global_label' => 'Pangkalahatan',
    ],
    'overview' => [
        'total_subscribers_label' => 'Kabuuang mga Subscriber',
        'total_subscribers_description' => 'Kabuuang bilang ng mga subscriber.',
    ],
];
