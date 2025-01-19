<?php

return [
    'resource_label' => 'API 密鑰|API 密鑰',
    'show_token' => [
        'heading' => '你的 API 令牌已生成',
        'description' => '請複製你的 API 令牌。爲了你的安全，它將不再顯示。',
        'copy_tooltip' => '令牌已複製！',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => '令牌名稱',
        'expires_at_label' => '過期時間',
        'expires_at_helper' => '在午夜過期。 留空表示無過期時間',
        'expires_at_validation' => '過期日期必須在未來',
        'abilities_label' => '權限',
        'abilities_hint' => '留空將給予權杖全部權限',
    ],
    'list' => [
        'actions' => [
            'revoke' => '撤銷',
        ],
        'headers' => [
            'name' => '令牌名稱',
            'abilities' => '权限',
            'created_at' => '創建時間',
            'expires_at' => '過期時間',
            'updated_at' => '更新時間',
        ],
    ],
];
