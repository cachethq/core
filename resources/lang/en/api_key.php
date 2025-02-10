<?php

return [
    'resource_label' => 'API Key|API Keys',
    'show_token' => [
        'heading' => 'Your API Token has been generated',
        'description' => 'Please copy your new API token. For your security, it won\'t be shown again.',
        'copy_tooltip' => 'Token copied!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Token Name',
        'expires_at_label' => 'Expires At',
        'expires_at_helper' => 'Expires at midnight. Leave empty for no expiry',
        'expires_at_validation' => 'The expiry date must be in the future',
        'abilities_label' => 'Permissions',
        'abilities_hint' => 'Leaving this empty will give the token full permissions',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Revoke',
        ],
        'headers' => [
            'name' => 'Token Name',
            'abilities' => 'Permissions',
            'created_at' => 'Created At',
            'expires_at' => 'Expires At',
            'updated_at' => 'Updated At',
        ],
    ],
];
