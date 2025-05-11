<?php

return [
    'resource_label' => 'API 키|API 키',
    'show_token' => [
        'heading' => 'API 토큰이 생성되었습니다',
        'description' => '새 API 토큰을 복사하세요. 보안을 위해 다시 표시되지 않습니다.',
        'copy_tooltip' => '토큰이 복사되었습니다!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => '토큰 이름',
        'expires_at_label' => '만료 시간',
        'expires_at_helper' => '자정에 만료됩니다. 만료 없음은 비워두세요',
        'expires_at_validation' => '만료 날짜는 미래 날짜여야 합니다',
        'abilities_label' => '권한',
        'abilities_hint' => '비워두면 토큰에 모든 권한이 부여됩니다',
    ],
    'list' => [
        'actions' => [
            'revoke' => '취소',
        ],
        'headers' => [
            'name' => '토큰 이름',
            'abilities' => '권한',
            'created_at' => '생성 시간',
            'expires_at' => '만료 시간',
            'updated_at' => '업데이트 시간',
        ],
    ],
];
