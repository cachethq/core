<?php

return [
    'title' => '사고',
    'resource_label' => '사고|사고',
    'status' => [
        'investigating' => '조사 중',
        'identified' => '원인 파악됨',
        'watching' => '모니터링 중',
        'fixed' => '해결됨',
        'reported' => '보고됨',
    ],
    'edit_button' => '사고 수정',
    'new_button' => '새 사고',
    'no_incidents_reported' => '보고된 사고가 없습니다.',
    'timeline' => [
        'past_incidents_header' => '과거 사고',
        'recent_incidents_header' => '최근 사고',
        'no_incidents_reported_between' => ':from부터 :to까지 보고된 사고가 없습니다',
        'navigate' => [
            'previous' => '이전',
            'today' => '오늘',
            'next' => '다음',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'visible' => '표시 여부',
            'stickied' => '고정 여부',
            'occurred_at' => '발생 시간',
            'notified_subscribers' => '구독자 알림 여부',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
            'deleted_at' => '삭제 시간',
        ],
        'actions' => [
            'record_update' => '업데이트 기록',
            'view_incident' => '사고 보기',
        ],
        'empty_state' => [
            'heading' => '사고',
            'description' => '사고는 서비스 상태를 전달하고 추적하는 데 사용됩니다.',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'status_label' => '상태',
        'message_label' => '메시지',
        'occurred_at_label' => '발생 시간',
        'occurred_at_helper' => '비워두면 사고의 생성 타임스탬프가 사용됩니다.',
        'visible_label' => '표시 여부',
        'user_label' => '사용자',
        'user_helper' => '사고를 보고한 사용자입니다.',
        'notifications_label' => '구독자에게 알림을 보내시겠습니까?',
        'stickied_label' => '사고를 고정하시겠습니까?',
        'guid_label' => '사고 UUID',
        'add_component' => [
            'action_label' => '구성 요소 추가',
            'header' => '구성 요소',
            'component_label' => '구성 요소',
            'status_label' => '상태',
        ],
    ],
    'record_update' => [
        'success_title' => '사고 :name 업데이트됨',
        'success_body' => '새로운 사고 업데이트가 기록되었습니다.',
        'form' => [
            'message_label' => '메시지',
            'status_label' => '상태',
            'user_label' => '사용자',
            'user_helper' => '이 사고를 보고한 사람입니다.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => '총 사고 수',
        'total_incidents_description' => '총 사고 수입니다.',
    ],
];
