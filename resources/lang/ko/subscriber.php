<?php

return [
    'resource_label' => '구독자|구독자',
    'list' => [
        'headers' => [
            'email' => '이메일',
            'verify_code' => '인증 코드',
            'global' => '전체',
            'phone_number' => '전화번호',
            'slack_webhook_url' => 'Slack 웹훅 URL',
            'verified_at' => '인증 시간',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
        ],
        'empty_state' => [
            'heading' => '구독자',
            'description' => '구독자는 상태 페이지의 알림을 구독한 사람들입니다.',
        ],
        'actions' => [
            'verify_label' => '인증',
        ],
    ],
    'form' => [
        'email_label' => '이메일',
        'verify_code_label' => '인증 코드',
        'verified_at_label' => '인증 시간',
        'global_label' => '전체',
    ],
    'overview' => [
        'total_subscribers_label' => '총 구독자 수',
        'total_subscribers_description' => '총 구독자 수입니다.',
    ],
];
