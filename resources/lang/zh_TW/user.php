<?php

return [
    'level' => [
        'admin' => '管理員',
        'user' => '用戶',
    ],
    'resource_label' => '用戶|用戶',
    'list' => [
        'headers' => [
            'name' => '名稱',
            'email' => '電子郵件地址',
            'email_verified_at' => '電子郵件驗證時間',
            'is_admin' => '是否是管理員？',
        ],
        'actions' => [
            'verify_email' => '驗證電子郵件',
        ],
    ],
    'form' => [
        'name_label' => '名稱',
        'email_label' => '電子郵件地址',
        'password_label' => '密碼',
        'password_confirmation_label' => '確認密碼',
        'preferred_locale' => '首選語言環境',
        'preferred_locale_system_default' => '系統默認',
        'is_admin_label' => '管理員',
    ],
];
