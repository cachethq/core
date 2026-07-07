<?php

return [
    'resource_label' => 'Subscriber|Subscribers',
    'list' => [
        'headers' => [
            'email' => 'Email',
            'phone_number' => 'Phone number',
            'slack_webhook_url' => 'Slack Webhook URL',
            'verified_at' => 'Verified at',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ],
        'empty_state' => [
            'heading' => 'Subscribers',
            'description' => 'Subscribers are people who have subscribed to your status page for notifications.',
        ],
        'actions' => [
            'verify_label' => 'Verify',
            'resend_verification_label' => 'Resend Verification',
        ],
    ],
    'resend_verification' => [
        'success_title' => 'Verification email sent',
        'success_body' => 'A new verification email has been sent to :email.',
    ],
    'form' => [
        'email_label' => 'Email',
        'verified_at_label' => 'Verified at',
    ],
    'overview' => [
        'total_subscribers_label' => 'Total Subscribers',
        'verified_subscribers_description' => ':count verified',
    ],
    'status_page' => [
        'subscribe' => [
            'title' => 'Subscribe to updates',
            'heading' => 'Subscribe to updates',
            'description' => 'Get an email whenever incidents are reported or maintenance is scheduled.',
            'consent' => 'We\'ll send you a confirmation email. You can unsubscribe at any time.',
            'back' => 'Back to status page',
            'button_label' => 'Subscribe to updates',
            'subscribed_heading' => 'Check your email',
            'subscribed_body' => 'If this email address isn\'t already subscribed, you\'ll receive an email with a link to confirm your subscription.',
            'verified_heading' => 'Subscription confirmed',
            'verified_body' => 'You\'ll now receive status updates by email.',
            'unsubscribed_heading' => 'You\'ve been unsubscribed',
            'unsubscribed_body' => 'You\'ll no longer receive status updates by email. You can subscribe again at any time.',
        ],
        'unsubscribe' => [
            'title' => 'Unsubscribe',
            'heading' => 'Unsubscribe from updates?',
            'body' => ':email will no longer receive status updates.',
            'button' => 'Unsubscribe',
            'cancel' => 'Keep my subscription',
        ],
        'email_label' => 'Email address',
        'email_placeholder' => 'you@example.com',
        'subscribe_button' => 'Subscribe',
    ],
    'mail' => [
        'unsubscribe' => 'Unsubscribe',
        'new_incident' => [
            'subject' => 'New incident: :incident',
            'button' => 'View incident',
        ],
        'incident_updated' => [
            'subject' => 'Incident updated: :incident',
            'button' => 'View incident',
        ],
        'new_schedule' => [
            'subject' => 'Scheduled maintenance: :schedule',
            'scheduled_for' => 'Scheduled for',
            'button' => 'View maintenance',
        ],
        'schedule_updated' => [
            'subject' => 'Maintenance updated: :schedule',
            'updated_at' => 'Updated',
            'button' => 'View maintenance',
        ],
        'schedule_rescheduled' => [
            'subject' => 'Maintenance rescheduled: :schedule',
            'previously' => 'Previously',
            'now' => 'Now scheduled for',
            'button' => 'View maintenance',
        ],
        'schedule_completed' => [
            'subject' => 'Maintenance complete: :schedule',
            'completed_at' => 'Completed',
            'body' => 'The scheduled maintenance has been completed and all affected services should now be operating normally.',
            'button' => 'View maintenance',
        ],
        'verify' => [
            'subject' => 'Confirm your subscription',
            'heading' => 'Confirm your subscription',
            'body' => 'You\'re receiving this email because someone subscribed to status updates from :app. Confirm your subscription to start receiving updates.',
            'button' => 'Confirm subscription',
            'ignore' => 'If you didn\'t subscribe to :app, you can safely ignore this email.',
        ],
    ],
];
