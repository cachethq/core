<?php

return [
    'title' => 'Vorfälle',
    'resource_label' => 'Vorfall|Vorfälle',
    'status' => [
        'investigating' => 'Untersuchung',
        'identified' => 'Identifiziert',
        'watching' => 'Beobachtung',
        'fixed' => 'Behoben',
        'reported' => 'Gemeldet',
    ],
    'edit_button' => 'Vorfall bearbeiten',
    'new_button' => 'Vorfall hinzufügen',
    'no_incidents_reported' => 'Keine Vorfälle gemeldet.',
    'timeline' => [
        'past_incidents_header' => 'Vergangene Vorfälle',
        'recent_incidents_header' => 'Jüngste Vorfälle',
        'no_incidents_reported_between' => 'Keine Vorfälle gemeldet zwischen :from und :to',
        'navigate' => [
            'previous' => 'Vor',
            'today' => 'Heute',
            'next' => 'Weiter',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'visible' => 'Sichtbar',
            'stickied' => 'Angeheftet',
            'occurred_at' => 'Aufgetreten am',
            'notified_subscribers' => 'Benachrichtigte Abonnenten',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'actions' => [
            'record_update' => 'Update aufzeichnen',
            'view_incident' => 'Vorfall anschauen',
        ],
        'empty_state' => [
            'heading' => 'Vorfälle',
            'description' => 'Vorfälle werden verwendet, um den Status Deiner Dienste zu kommunizieren und zu verfolgen.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'message_label' => 'Nachricht',
        'occurred_at_label' => 'Aufgetreten am',
        'occurred_at_helper' => 'Wenn dieses Feld leer gelassen wird, wird der Zeitstempel der Erstellung des Vorfalls verwendet.',
        'visible_label' => 'Sichtbar',
        'user_label' => 'Benutzer',
        'user_helper' => 'Benutzer, welcher den Vorfall gemeldet hat.',
        'notifications_label' => 'Abonnenten benachrichtigen?',
        'stickied_label' => 'Vorfall anheften?',
        'guid_label' => 'Vorfall-UUID',
        'add_component' => [
            'action_label' => 'Komponente hinzufügen',
            'header' => 'Komponenten',
            'component_label' => 'Komponente',
            'status_label' => 'Status',
        ],
    ],
    'record_update' => [
        'success_title' => 'Vorfall aktualisiert - :name',
        'success_body' => 'Ein Update zu einem Vorfall wurde veröffentlicht.',
        'form' => [
            'message_label' => 'Nachricht',
            'status_label' => 'Status',
            'user_label' => 'Benutzer',
            'user_helper' => 'Wer diesen Vorfall gemeldet hat.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Vorfälle insgesamt',
        'total_incidents_description' => 'Anzahl aller Vorfälle',
    ],
];
