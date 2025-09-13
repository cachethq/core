<?php

return [
    'level' => [
        'admin' => '관리자',
        'user' => '사용자',
    ],
    'resource_label' => '사용자|사용자',
    'list' => [
        'headers' => [
            'name' => '이름',
            'email' => '이메일 주소',
            'email_verified_at' => '이메일 인증 시간',
            'is_admin' => '관리자 여부',
        ],
        'actions' => [
            'verify_email' => '이메일 인증',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'email_label' => '이메일 주소',
        'password_label' => '비밀번호',
        'password_confirmation_label' => '비밀번호 확인',
        'preferred_locale' => '선호 언어',
        'preferred_locale_system_default' => '시스템 기본값',
        'is_admin_label' => '관리자',
    ],
];
