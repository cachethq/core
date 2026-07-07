<?php

return [
    'system_health' => [
        'empty' => 'No components have been configured yet.',
        'add_component' => 'Add a component',
    ],
    'open_incidents' => [
        'heading' => 'Open Incidents',
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'occurred_at' => 'Occurred',
            'components' => 'Affected components',
        ],
        'actions' => [
            'create' => 'Create Incident',
        ],
        'empty_state' => [
            'heading' => 'No open incidents',
            'description' => 'All clear. Incidents that have not been fixed will show up here.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Maintenance',
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'scheduled_at' => 'Scheduled for',
        ],
        'actions' => [
            'create' => 'Schedule Maintenance',
        ],
        'empty_state' => [
            'heading' => 'No scheduled maintenance',
            'description' => 'Upcoming and in-progress maintenance windows will show up here.',
        ],
    ],
];
