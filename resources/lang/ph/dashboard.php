<?php

return [
    'system_health' => [
        'empty' => 'Wala pang naka-configure na komponent.',
        'add_component' => 'Magdagdag ng komponent',
    ],
    'open_incidents' => [
        'heading' => 'Mga Bukas na Insidente',
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'Kalagayan',
            'occurred_at' => 'Nangyari noong',
            'components' => 'Mga apektadong komponent',
        ],
        'actions' => [
            'create' => 'Gumawa ng Insidente',
        ],
        'empty_state' => [
            'heading' => 'Walang bukas na insidente',
            'description' => 'Maayos ang lahat. Ang mga insidenteng hindi pa naaayos ay lalabas dito.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Maintenance',
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'Kalagayan',
            'scheduled_at' => 'Naka-iskedyul sa',
        ],
        'actions' => [
            'create' => 'Mag-iskedyul ng Maintenance',
        ],
        'empty_state' => [
            'heading' => 'Walang naka-iskedyul na maintenance',
            'description' => 'Ang mga darating at isinasagawang maintenance ay lalabas dito.',
        ],
    ],
];
