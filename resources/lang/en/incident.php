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
    'edit_button' => 'Edit incident',
    'new_button' => 'New incident',
    'no_incidents_reported' => 'No incidents reported.',
    'affected_components_header' => 'Affected components',
    'timeline' => [
        'past_incidents_header' => 'Past incidents',
        'recent_incidents_header' => 'Recent incidents',
        'no_incidents_reported_between' => 'No incidents reported between :from and :to',
        'navigate' => [
            'previous' => 'Previous',
            'today' => 'Today',
            'next' => 'Next',
            'timeline' => 'Back to timeline',
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
            'record_update' => 'Record update',
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
        'notify_subscribers_label' => 'Notify subscribers of this incident.',
        'pin_incident_label' => 'Pin the incident to the top of the status page.',
        'guid_label' => 'Incident UUID',
        'meta_label' => 'Meta',
        'add_component' => [
            'action_label' => 'Add component',
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
        'new_update_label' => 'New update',
        'success_title' => 'Update recorded',
        'success_body' => 'You posted a new update to :name.',
        'form' => [
            'message_label' => 'Message',
            'status_label' => 'Status',
            'user_label' => 'User',
            'user_helper' => 'The user who reported the incident.',
        ],
    ],
    'overview' => [
        'open_incidents_label' => 'Open incidents',
        'open_incidents_description' => 'Incidents that have not been fixed.',
    ],
];
