<?php

return [
    'resource_label' => '구성 요소 그룹|구성 요소 그룹',
    'incident_count' => ':count 사고|:count 사고',
    'visibility' => [
        'expanded' => '항상 확장됨',
        'collapsed' => '항상 축소됨',
        'collapsed_unless_incident' => '진행 중인 사고가 없으면 축소됨',
    ],
    'list' => [
        'headers' => [
            'name' => '이름',
            'visible' => '표시 여부',
            'collapsed' => '축소 여부',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
        ],
        'empty_state' => [
            'heading' => '구성 요소 그룹',
            'description' => '관련 구성 요소를 함께 그룹화합니다.',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'visible_label' => '표시 여부',
        'collapsed_label' => '축소 여부',
    ],
];
