<?php

return [
    'resource_label' => '메트릭|메트릭',
    'list' => [
        'headers' => [
            'name' => '이름',
            'suffix' => '접미사',
            'default_value' => '기본값',
            'calc_type' => '메트릭 유형',
            'display_chart' => '차트 표시',
            'places' => '소수점 자릿수',
            'default_view' => '기본 보기',
            'threshold' => '임계값',
            'order' => '순서',
            'visible' => '표시 여부',
            'points_count' => '포인트 수',
            'created_at' => '생성 시간',
            'updated_at' => '업데이트 시간',
        ],
        'empty_state' => [
            'heading' => '메트릭',
            'description' => '메트릭은 데이터를 추적하고 표시하는 데 사용됩니다.',
        ],
    ],
    'form' => [
        'name_label' => '이름',
        'suffix_label' => '접미사',
        'description_label' => '설명',
        'default_view_label' => '기본 보기',
        'default_value_label' => '기본값',
        'calc_type_label' => '메트릭 유형',
        'places_label' => '소수점 자릿수',
        'threshold_label' => '임계값',

        'visible_label' => '표시 여부',
        'display_chart_label' => '차트 표시',
    ],
    'overview' => [
        'metric_points_label' => '메트릭 포인트',
        'metric_points_description' => '최근 메트릭 포인트입니다.',
    ],
    'sum_label' => '합계',
    'average_label' => '평균',
    'view_labels' => [
        'last_hour' => '지난 1시간',
        'today' => '오늘',
        'week' => '주간',
        'month' => '월간',
    ],
];
