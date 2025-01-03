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
    'record_update' => [
        'success_title' => 'Incident :name Updated',
        'success_body' => 'A new incident update has been recorded.',
        'form' => [
            'message_label' => 'Message',
            'status_label' => 'Status',
            'user_label' => 'User',
            'user_helper' => 'Who reported this incident.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Total Incidents',
        'total_incidents_description' => 'Total number of incidents.',
    ],
];
