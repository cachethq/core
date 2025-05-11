<?php

return [
    'resource_label' => '구성 요소|구성 요소',
    'list' => [
        'headers' => [
            'name' => '이름',
            'status' => '상태',
            'order' => '순서',
            'group' => '그룹',
            'enabled' => '활성화 여부',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
            'deleted_at' => '삭제 시간',
        ],
        'empty_state' => [
            'heading' => '구성 요소',
            'description' => '구성 요소는 상태 페이지의 상태에 영향을 줄 수 있는 시스템의 다양한 부분을 나타냅니다.',
        ],
    ],
    'last_updated' => '마지막 업데이트: :timestamp',
    'view_details' => '상세 보기',
    'form' => [
        'name_label' => '이름',
        'status_label' => '상태',
        'description_label' => '설명',
        'component_group_label' => '구성 요소 그룹',
        'link_label' => '링크',
        'link_helper' => '구성 요소에 대한 선택적 링크입니다.',
    ],
    'status' => [
        'operational' => '정상 작동 중',
        'performance_issues' => '성능 문제',
        'partial_outage' => '부분 장애',
        'major_outage' => '주요 장애',
        'unknown' => '알 수 없음',
    ],
];
