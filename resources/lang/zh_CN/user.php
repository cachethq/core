<?php

return [
    'level' => [
        'admin' => '管理员',
        'user' => '用户',
    ],
    'resource_label' => '用户|用户',
    'list' => [
        'headers' => [
            'name' => '名称',
            'email' => '电子邮件地址',
            'email_verified_at' => '电子邮件验证时间',
            'is_admin' => '是否是管理员？',
        ],
        'actions' => [
            'verify_email' => '验证电子邮件',
        ],
    ],
    'form' => [
        'name_label' => '名称',
        'email_label' => '电子邮件地址',
        'password_label' => '密码',
        'password_confirmation_label' => '确认密码',
        'preferred_locale' => '首选语言环境',
        'preferred_locale_system_default' => '系统默认',
        'is_admin_label' => '管理员',
    ],
];
