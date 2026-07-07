<?php

return [
    'title' => 'Incidents',
    'resource_label' => 'Incident|Incidents',
    'status' => [
        'investigating' => 'Investigating',
        'identified' => 'Identified',
        'watching' => 'Watching',
        'fixed' => 'Fixed',
        'reported' => 'Reported',
    ],
    'edit_button' => 'Edit Incident',
    'new_button' => 'New Incident',
    'no_incidents_reported' => 'No incidents reported.',
    'affected_components_header' => 'Affected Components',
    'timeline' => [
        'past_incidents_header' => 'Past Incidents',
        'recent_incidents_header' => 'Recent Incidents',
        'no_incidents_reported_between' => 'No incidents reported between :from and :to',
        'navigate' => [
            'previous' => 'Previous',
            'today' => 'Today',
            'next' => 'Next',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'visible' => 'Visible',
            'stickied' => 'Stickied',
            'occurred_at' => 'Occurred at',
            'notified_subscribers' => 'Notified subscribers',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'deleted_at' => 'Deleted at',
        ],
        'actions' => [
            'record_update' => 'Record Update',
            'view_incident' => 'View incident',
        ],
        'empty_state' => [
            'heading' => 'Incidents',
            'description' => 'Incidents are used to communicate and track the status of your services.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'message_label' => 'Message',
        'occurred_at_label' => 'Occurred at',
        'occurred_at_helper' => 'The incident\'s created timestamp will be used if left empty.',
        'visible_label' => 'Visible',
        'user_label' => 'User',
        'user_helper' => 'The user who reported the incident.',
        'notifications_label' => 'Notify Subscribers?',
        'stickied_label' => 'Sticky Incident?',
        'guid_label' => 'Incident UUID',
        'add_component' => [
            'action_label' => 'Add Component',
            'header' => 'Components',
            'component_label' => 'Component',
            'status_label' => 'Status',
        ],
    ],
    'mail' => [
        'long_running' => [
            'subject' => 'Incident needs attention: :incident',
            'heading' => 'This incident needs attention',
            'body' => 'There has been no activity on this incident since :since. Consider posting an update to keep your subscribers informed, or mark it as fixed.',
            'button' => 'Manage incident',
        ],
    ],
    'record_update' => [
        'new_update_label' => 'New Update',
        'success_title' => 'Update recorded',
        'success_body' => 'A new update has been posted to :name.',
        'form' => [
            'message_label' => 'Message',
            'status_label' => 'Status',
            'user_label' => 'User',
            'user_helper' => 'Who reported this incident.',
        ],
    ],
    'overview' => [
        'open_incidents_label' => 'Open Incidents',
        'open_incidents_description' => 'Incidents that have not been fixed.',
    ],
];
