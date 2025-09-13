<?php

return [
    'resource_label' => '일정|일정',
    'list' => [
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'scheduled_at' => '예정 시간',
            'completed_at' => '완료 시간',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
            'deleted_at' => '삭제 시간',
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
