<?php

return [
    'resource_label' => 'Component|Components',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'order' => 'Order',
            'group' => 'Group',
            'enabled' => 'Enabled',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'deleted_at' => 'Deleted at',
        ],
        'empty_state' => [
            'heading' => 'Components',
            'description' => 'Components represent the various parts of your system that can affect the status of your status page.',
        ],
    ],
    'last_updated' => 'Last updated :timestamp',
    'view_details' => 'View Details',
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'description_label' => 'Description',
        'component_group_label' => 'Component Group',
        'link_label' => 'Link',
        'link_helper' => 'An optional link to the component.',
    ],
    'status' => [
        'operational' => 'Operational',
        'performance_issues' => 'Performance Issues',
        'partial_outage' => 'Partial Outage',
        'major_outage' => 'Major Outage',
        'under_maintenance' => 'Under maintenance',
        'unknown' => 'Unknown',
    ],

];
