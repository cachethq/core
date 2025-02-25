<?php

return [
    'resource_label' => 'Iskedyul|Mga Iskedyul',
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'kalagayan',
            'scheduled_at' => 'Naka-iskedyul Noong',
            'completed_at' => 'Natapos Noong',
            'created_at' => 'Ginawa Noong',
            'updated_at' => 'Na-update Noong',
            'deleted_at' => 'Tinanggal Noong',
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
