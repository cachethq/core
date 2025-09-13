<?php

return [
    'resource_label' => '웹훅|웹훅',
    'event_selection' => [
        'all' => '모든 이벤트 전송',
        'selected' => '선택한 이벤트만 전송',
    ],
    'form' => [
        'url_label' => '페이로드 URL',
        'url_helper' => '이벤트는 이 URL로 POST됩니다.',
        'secret_label' => '시크릿',
        'secret_helper' => '페이로드는 이 시크릿으로 서명됩니다. 자세한 내용은 *웹훅 문서*를 참조하세요.',
        'description_label' => '설명',
        'event_selection_label' => '모든 이벤트를 전송하시겠습니까?',
        'events_label' => '이벤트',
        'edit_secret_label' => '시크릿 수정',
        'update_secret_label' => '시크릿 업데이트',
    ],
    'attempts' => [
        'heading' => '시도',
        'empty_state' => '아직 이 웹훅에 대한 시도가 없습니다',
    ],
    'list' => [
        'headers' => [
            'url' => '페이로드 URL',
            'success_rate_24h' => '성공률 (24시간)',
        ],
    ],
];
