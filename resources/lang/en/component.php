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
            'checked' => 'Monitored',
            'checked_at' => 'Checked at',
            'updated_at' => 'Updated at',
            'deleted_at' => 'Deleted at',
        ],
        'empty_state' => [
            'heading' => 'Components',
            'description' => 'Components represent the various parts of your system that can affect the status of your status page.',
        ],
    ],
    'last_updated' => 'Last updated :timestamp',
    'view_details' => 'View details',
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'description_label' => 'Description',
        'component_group_label' => 'Component group',
        'link_label' => 'Link',
        'link_helper' => 'An optional link to the component.',
        'availability_section_title' => 'Availability',
        'enabled_label' => 'Enabled',
        'checked_label' => 'Monitoring',
        'checked_helper' => 'Periodically check this component and record its availability.',
    ],
    'status' => [
        'operational' => 'Operational',
        'performance_issues' => 'Performance issues',
        'partial_outage' => 'Partial outage',
        'major_outage' => 'Major outage',
        'under_maintenance' => 'Under maintenance',
        'unknown' => 'Unknown',
    ],
    'status_source' => [
        'manual' => 'Manual',
        'monitor' => 'Monitoring',
        'import' => 'Import',
        'system' => 'System',
    ],
    'overview' => [
        'operational_components_label' => 'Operational components',
        'operational_components_description' => 'Components that are fully operational.',
    ],
    'checks' => [
        'title' => 'Recent checks',
        'empty_state' => [
            'heading' => 'No checks yet',
            'description' => 'Checks will appear here once the component has been monitored.',
        ],
        'headers' => [
            'status' => 'Status',
            'successful' => 'Successful',
            'response_code' => 'Response code',
            'response_time' => 'Response time',
            'checked_at' => 'Checked at',
        ],
    ],

];
