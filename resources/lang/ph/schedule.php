<?php

return [
    'resource_label' => 'Iskedyul|Mga Iskedyul',
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'kalagayan',
            'scheduled_at' => 'Naka-iskedyul Noong',
            'completed_at' => 'Natapos Noong',
            'published_at' => 'Nai-publish Noong',
            'created_at' => 'Ginawa Noong',
            'updated_at' => 'Na-update Noong',
            'deleted_at' => 'Tinanggal Noong',
        ],
        'published_now' => 'Nai-publish',
        'filters' => [
            'scheduled' => 'Naka-iskedyul (hindi pa nai-publish)',
        ],
        'empty_state' => [
            'heading' => 'Mga Iskedyul',
            'description' => 'Planuhin at ischedule ang iyong mga maintenance.',
        ],
        'actions' => [
            'record_update' => 'I-record ang Update',
            'complete' => 'Kumpletuhin ang Maintenance',
        ],
    ],
    'form' => [
        'name_label' => 'Pangalan',
        'message_label' => 'Mensahe',
        'scheduled_at_label' => 'Naka-iskedyul Noong',
        'completed_at_label' => 'Natapos Noong',
        'published_at_label' => 'Nai-publish Noong',
        'published_at_helper' => 'I-iskedyul ang maintenance na lumitaw sa status page sa hinaharap na petsa. Nakatago ito sa lahat hanggang sa oras na iyon. Iwanang walang laman upang i-publish kaagad.',
    ],
    'add_update' => [
        'success_title' => 'Iskedyul :name Na-update',
        'success_body' => 'Isang bagong update ng iskedyul ang na-record.',
        'form' => [
            'message_label' => 'Mensahe',
            'completed_at_label' => 'Natapos Noong',
        ],
    ],
    'status' => [
        'upcoming' => 'Darating',
        'in_progress' => 'Isinasagawa',
        'complete' => 'Kumpleto',
    ],
    'planned_maintenance_header' => 'Nakaiskedyul na Maintenance',
];
