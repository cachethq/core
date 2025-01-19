<?php

return [
    'resource_label' => 'API 密钥|API 密钥',
    'show_token' => [
        'heading' => '你的 API 令牌已生成',
        'description' => '请复制你的新 API 令牌。为了你的安全，它将不会再次显示。',
        'copy_tooltip' => '令牌已复制！',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => '令牌名称',
        'expires_at_label' => '过期时间',
        'expires_at_helper' => '在午夜过期。留空表示无过期时间',
        'expires_at_validation' => '过期日期必须在未来',
        'abilities_label' => '权限',
        'abilities_hint' => '留空将给予令牌全部权限',
    ],
    'list' => [
        'actions' => [
            'revoke' => '撤销',
        ],
        'headers' => [
            'name' => '令牌名称',
            'abilities' => '权限',
            'created_at' => '创建时间',
            'expires_at' => '过期时间',
            'updated_at' => '更新时间',
        ],
    ],
];
