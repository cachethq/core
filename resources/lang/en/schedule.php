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
            'record_update' => 'Record update',
            'complete' => 'Complete maintenance',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'message_label' => 'Message',
        'scheduled_at_label' => 'Scheduled at',
        'scheduled_at_helper' => 'When the maintenance window begins.',
        'completed_at_label' => 'Completed at',
        'completed_at_helper' => 'When the maintenance window is expected to end.',
        'notify_subscribers_label' => 'Notify subscribers of this maintenance.',
        'notifications_helper' => 'Email subscribers about this scheduled maintenance and its updates.',
        'meta_label' => 'Meta',
        'add_component' => [
            'action_label' => 'Add component',
            'header' => 'Affected components',
            'component_label' => 'Component',
        ],
    ],
    'add_update' => [
        'new_update_label' => 'New update',
        'success_title' => 'Update recorded',
        'success_body' => 'You posted a new update to :name.',
        'form' => [
            'message_label' => 'Message',
            'completed_at_label' => 'Completed at',
        ],
    ],
    'status' => [
        'upcoming' => 'Upcoming',
        'in_progress' => 'In progress',
        'complete' => 'Complete',
    ],
    'planned_maintenance_header' => 'Planned maintenance',
];
