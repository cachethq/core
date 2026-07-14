<?php

return [
    'resource_label' => 'Component Group|Component Groups',
    'incident_count' => ':count Incident|:count Incidents',
    'visibility' => [
        'expanded' => 'Always expanded',
        'collapsed' => 'Always collapsed',
        'collapsed_unless_incident' => 'Collapsed unless ongoing incident',
    ],
    'list' => [
        'headers' => [
            'name' => 'Name',
            'visible' => 'Visible',
            'collapsed' => 'Collapsed',
            'order_column' => 'Component group order',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ],
        'empty_state' => [
            'heading' => 'Component groups',
            'description' => 'Group related components together.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'visible_label' => 'Visible',
        'collapsed_label' => 'Collapsed',
        'order_column_label' => 'Component group order',
        'order_direction' => 'Order direction',
        'meta_label' => 'Meta',
    ],
];
