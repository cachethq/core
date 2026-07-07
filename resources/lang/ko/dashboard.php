<?php

return [
    'system_health' => [
        'empty' => '아직 구성된 구성 요소가 없습니다.',
        'add_component' => '구성 요소 추가',
    ],
    'open_incidents' => [
        'heading' => '진행 중인 사고',
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'occurred_at' => '발생 시간',
            'components' => '영향받는 구성 요소',
        ],
        'actions' => [
            'create' => '사고 생성',
        ],
        'empty_state' => [
            'heading' => '진행 중인 사고 없음',
            'description' => '모두 정상입니다. 아직 해결되지 않은 사고가 여기에 표시됩니다.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => '유지보수',
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'scheduled_at' => '예정 시간',
        ],
        'actions' => [
            'create' => '유지보수 일정 잡기',
        ],
        'empty_state' => [
            'heading' => '예정된 유지보수 없음',
            'description' => '예정되었거나 진행 중인 유지보수가 여기에 표시됩니다.',
        ],
    ],
];
