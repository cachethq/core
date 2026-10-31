<?php

return [
    'resource_label' => 'Abonnent|Abonnenten',
    'list' => [
        'headers' => [
            'email' => 'E-Mail',
            'phone_number' => 'Telefonnummer',
            'slack_webhook_url' => 'Slack-Webhook-URL',
            'verified_at' => 'Verifiziert am',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
        ],
        'empty_state' => [
            'heading' => 'Abonnenten',
            'description' => 'Abonnenten sind Personen, welche die Statusseite für Benachrichtigungen abonniert haben.',
        ],
        'actions' => [
            'verify_label' => 'Verifizieren',
            'resend_verification_label' => 'Bestätigung erneut senden',
        ],
    ],
    'resend_verification' => [
        'success_title' => 'Bestätigungs-E-Mail gesendet',
        'success_body' => 'Eine neue Bestätigungs-E-Mail wurde an :email gesendet.',
    ],
    'form' => [
        'email_label' => 'E-Mail',
        'verified_at_label' => 'Verifiziert am',
        'meta_label' => 'Metadaten',
    ],
    'overview' => [
        'total_subscribers_label' => 'Abonnenten insgesamt',
        'verified_subscribers_description' => ':count verifiziert',
    ],
    'status_page' => [
        'subscribe' => [
            'title' => 'Updates abonnieren',
            'heading' => 'Updates abonnieren',
            'description' => 'Erhalte eine E-Mail, sobald Vorfälle gemeldet oder Wartungen geplant werden.',
            'consent' => 'Wir senden dir eine Bestätigungs-E-Mail. Du kannst dich jederzeit wieder abmelden.',
            'back' => 'Zurück zur Statusseite',
            'button_label' => 'Abonnieren',
            'subscribed_heading' => 'Prüfe dein Postfach',
            'subscribed_body' => 'Falls diese E-Mail-Adresse noch nicht angemeldet ist, erhältst du eine E-Mail mit einem Link zur Bestätigung deines Abonnements.',
            'verified_heading' => 'Abonnement bestätigt',
            'verified_body' => 'Du erhältst ab jetzt Status-Updates per E-Mail.',
            'unsubscribed_heading' => 'Du wurdest abgemeldet',
            'unsubscribed_body' => 'Du erhältst keine Status-Updates mehr per E-Mail. Du kannst dich jederzeit erneut anmelden.',
        ],
        'unsubscribe' => [
            'title' => 'Abmelden',
            'heading' => 'Von Updates abmelden?',
            'body' => ':email erhält dann keine Status-Updates mehr.',
            'button' => 'Abmelden',
            'cancel' => 'Abonnement behalten',
        ],
        'email_label' => 'E-Mail-Adresse',
        'email_placeholder' => 'du@example.com',
        'subscribe_button' => 'Abonnieren',
    ],
    'mail' => [
        'unsubscribe' => 'Abmelden',
        'new_incident' => [
            'subject' => 'Neuer Vorfall: :incident',
            'button' => 'Vorfall ansehen',
        ],
        'incident_updated' => [
            'subject' => 'Vorfall aktualisiert: :incident',
            'button' => 'Vorfall ansehen',
        ],
        'new_schedule' => [
            'subject' => 'Geplante Wartung: :schedule',
            'scheduled_for' => 'Geplant für',
            'button' => 'Wartung ansehen',
        ],
        'schedule_updated' => [
            'subject' => 'Wartung aktualisiert: :schedule',
            'updated_at' => 'Aktualisiert',
            'button' => 'Wartung ansehen',
        ],
        'schedule_rescheduled' => [
            'subject' => 'Wartung verschoben: :schedule',
            'previously' => 'Bisher',
            'now' => 'Jetzt geplant für',
            'button' => 'Wartung ansehen',
        ],
        'schedule_completed' => [
            'subject' => 'Wartung abgeschlossen: :schedule',
            'completed_at' => 'Abgeschlossen',
            'body' => 'Die geplante Wartung ist abgeschlossen. Alle betroffenen Dienste sollten jetzt wieder normal funktionieren.',
            'button' => 'Wartung ansehen',
        ],
        'verify' => [
            'subject' => 'Bestätige dein Abonnement',
            'heading' => 'Bestätige dein Abonnement',
            'body' => 'Du erhältst diese E-Mail, weil jemand die Status-Updates von :app abonniert hat. Bestätige dein Abonnement, um Updates zu erhalten.',
            'button' => 'Abonnement bestätigen',
            'ignore' => 'Falls du :app nicht abonniert hast, kannst du diese E-Mail einfach ignorieren.',
        ],
    ],
];
