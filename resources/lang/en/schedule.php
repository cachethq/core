<?php

return [
    'resource_label' => 'Schedule|Schedules',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'scheduled_at' => 'Scheduled at',
            'completed_at' => 'Completed at',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'deleted_at' => 'Deleted at',
        ],
        'empty_state' => [
            'heading' => 'Schedules',
            'description' => 'Plan and schedule your maintenance.',
        ],
        'actions' => [
            'record_update' => 'Record Update',
            'complete' => 'Complete Maintenance',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'message_label' => 'Message',
        'scheduled_at_label' => 'Scheduled at',
        'scheduled_at_helper' => 'When the maintenance window begins.',
        'completed_at_label' => 'Completed at',
        'completed_at_helper' => 'When the maintenance window is expected to end.',
        'notifications_label' => 'Notify Subscribers?',
        'notifications_helper' => 'Email subscribers about this scheduled maintenance and its updates.',
        'add_component' => [
            'action_label' => 'Add Component',
            'header' => 'Affected Components',
            'component_label' => 'Component',
        ],
    ],
    'add_update' => [
        'new_update_label' => 'New Update',
        'success_title' => 'Update recorded',
        'success_body' => 'A new update has been posted to :name.',
        'form' => [
            'message_label' => 'Message',
            'completed_at_label' => 'Completed at',
        ],
    ],
    'status' => [
        'upcoming' => 'Upcoming',
        'in_progress' => 'In Progress',
        'complete' => 'Complete',
    ],
    'planned_maintenance_header' => 'Planned Maintenance',
];
