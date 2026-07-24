<?php

return [
    'resource_label' => '일정|일정',
    'list' => [
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'scheduled_at' => '예정 시간',
            'completed_at' => '완료 시간',
            'published_at' => '게시 시간',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
            'deleted_at' => '삭제 시간',
        ],
        'published_now' => '게시됨',
        'filters' => [
            'scheduled' => '예정됨 (아직 게시되지 않음)',
        ],
        'empty_state' => [
            'heading' => '일정',
            'description' => '유지보수를 계획하고 일정을 잡으세요.',
        ],
        'actions' => [
            'record_update' => '업데이트 기록',
            'complete' => '유지보수 완료',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'message_label' => '메시지',
        'scheduled_at_label' => '예정 시간',
        'completed_at_label' => '완료 시간',
        'published_at_label' => '게시 시간',
        'published_at_helper' => '유지보수가 미래의 특정 날짜에 상태 페이지에 표시되도록 예약합니다. 그때까지는 모두에게 숨겨집니다. 비워두면 즉시 게시됩니다.',
    ],
    'add_update' => [
        'success_title' => '일정 :name 업데이트됨',
        'success_body' => '새로운 일정 업데이트가 기록되었습니다.',
        'form' => [
            'message_label' => '메시지',
            'completed_at_label' => '완료 시간',
        ],
    ],
    'status' => [
        'upcoming' => '예정됨',
        'in_progress' => '진행 중',
        'complete' => '완료됨',
    ],
    'planned_maintenance_header' => '예정된 유지보수',
];
